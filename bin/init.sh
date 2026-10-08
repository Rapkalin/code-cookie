#!/usr/bin/env bash
# =============================================================================
# Initialisation WordPress (exécutée par l'entrypoint du conteneur PHP).
#
# IDEMPOTENT : si WordPress est déjà installé, ni l'import ni l'installation ne
# rejouent. C'est ce qui permet de redémarrer le conteneur sans écraser la base.
#
# wp cible website/wordpress-core grâce à wp-cli.yml (path:) à la racine.
# Le script tourne en root dans le conteneur -> toutes les commandes wp ont
# besoin de --allow-root.
# =============================================================================
set -e

APP_DIR="/var/www/html"
cd "$APP_DIR"

if [ -f "$APP_DIR/website/.env" ]; then
    ENV_FILE="$APP_DIR/website/.env"
else
    ENV_FILE="$APP_DIR/.env"
fi

echo "==> [init] Initialisation WordPress"

if [ -f "$ENV_FILE" ]; then
    set -a
    # shellcheck disable=SC1090
    . "$ENV_FILE"
    set +a
fi

# `wp db …` est INUTILISABLE sur cette pile, et ce n'est pas rattrapable : ces
# sous-commandes délèguent au client mariadb avec leur propre --defaults-file,
# qui écarte /root/.my.cnf — or MySQL 8.4 présente un certificat auto-signé.
# Vérifié le 26/09/2026 : `wp db import`, `wp db query` et `wp db export`
# échouent tous sur « self-signed certificate in certificate chain », y compris
# avec --skip-ssl, l'option n'atteignant pas la requête interne de WP-CLI.
# Le client appelé directement lit /root/.my.cnf et passe. PHP (mysqli) n'est
# pas concerné : `wp search-replace`, `wp eval` et `wp core` fonctionnent.
db_client() {
    mariadb -h "${DATABASE_HOST:-db}" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$@" "$DATABASE_NAME"
}

WP_HOME="${WP_HOME:-http://localhost:8030}"
WP_TITLE="${WP_TITLE:-Code Cookie}"
WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WP_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@code-cookie.local}"

# -----------------------------------------------------------------------------
# 1) Générer les sels encore à 'generateme' dans le .env.
#    Chaînes alphanumériques de 64 caractères : sans | ni /, donc sûres pour sed.
# -----------------------------------------------------------------------------
SALT_KEYS="AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT"

if [ -f "$ENV_FILE" ]; then
    for KEY in $SALT_KEYS; do
        if grep -q "^${KEY}='generateme'" "$ENV_FILE"; then
            SALT="$(tr -dc 'A-Za-z0-9' </dev/urandom | head -c 64)"
            sed -i "s|^${KEY}='generateme'|${KEY}='${SALT}'|" "$ENV_FILE"
            echo "==> [init] Sel généré pour ${KEY}"
        fi
    done
else
    echo "!!! [init] .env introuvable : génération des sels ignorée."
fi

# -----------------------------------------------------------------------------
# 2) Droits d'écriture sur les dossiers que WordPress et ses extensions
#    écrivent. Le chown peut échouer selon l'hôte (bind mount) : on tolère
#    l'échec plutôt que de bloquer le démarrage.
# -----------------------------------------------------------------------------
echo "==> [init] Droits d'écriture sur les dossiers de contenu"
for DIR in website/app/uploads website/app/cache website/app/languages website/app/w3tc-config; do
    mkdir -p "$APP_DIR/$DIR"
    chown -R www-data:www-data "$APP_DIR/$DIR" 2>/dev/null || true
    chmod -R u+rwX,g+rwX "$APP_DIR/$DIR" 2>/dev/null || true
done

# -----------------------------------------------------------------------------
# 3) Base de données.
#
#    Trois cas, dans cet ordre :
#      a) WordPress déjà installé  -> on ne touche à rien ;
#      b) un dump attend dans exports/ -> on l'importe ;
#      c) rien                     -> installation neuve.
#
#    `wp core is-installed` échoue tant qu'aucune table n'existe : c'est la
#    sonde, et elle ne coûte rien.
# -----------------------------------------------------------------------------
if wp core is-installed --allow-root 2>/dev/null; then
    echo "==> [init] WordPress déjà installé : import et installation sautés."
else
    # Le dump le plus RÉCENT par date de modification. `ls -t` plutôt qu'un tri
    # sur le nom : un export nommé à la main ne suit pas forcément le format
    # export-AAAAMMJJ_HHMMSS.sql des scripts.
    DUMP="$(ls -t "$APP_DIR"/exports/*.sql "$APP_DIR"/exports/*.sql.gz 2>/dev/null | head -n 1 || true)"

    if [ -n "$DUMP" ]; then
        echo "==> [init] Import du dump : $(basename "$DUMP")"
        case "$DUMP" in
            *.gz) gunzip -c "$DUMP" | db_client ;;
            *)    db_client < "$DUMP" ;;
        esac
        echo "==> [init] Dump importé."

        # L'URL du dump est celle de la PRODUCTION. On la lit dans la base
        # plutôt que de la supposer : `wp option get home` renverrait la
        # constante WP_HOME de wp-config.php, donc la valeur locale, et la
        # comparaison serait toujours vraie.
        TABLE_PREFIX="${DATABASE_PREFIX:-wp_}"
        OLD_HOME="$(db_client -N -e "SELECT option_value FROM ${TABLE_PREFIX}options WHERE option_name='home' LIMIT 1;" 2>/dev/null | tr -d '[:space:]' || true)"

        if [ -n "$OLD_HOME" ] && [ "$OLD_HOME" != "$WP_HOME" ]; then
            echo "==> [init] Réécriture des URLs : ${OLD_HOME} -> ${WP_HOME}"
            # --skip-columns=guid : les GUID sont des identifiants historiques,
            # pas des URLs à suivre. Les réécrire fait réapparaître d'anciens
            # articles comme neufs dans les lecteurs de flux.
            wp search-replace "$OLD_HOME" "$WP_HOME" \
                --all-tables --precise --skip-columns=guid --report-changed-only --allow-root || \
                echo "!!! [init] Réécriture des URLs échouée (à faire à la main)."
        else
            echo "==> [init] URLs du dump déjà alignées sur ${WP_HOME}."
        fi
    else
        echo "==> [init] Aucun dump dans exports/ : installation neuve sur ${WP_HOME}"
        wp core install \
            --url="$WP_HOME" \
            --title="$WP_TITLE" \
            --admin_user="$WP_ADMIN_USER" \
            --admin_password="$WP_ADMIN_PASSWORD" \
            --admin_email="$WP_ADMIN_EMAIL" \
            --skip-email \
            --allow-root

        echo "==> [init] Permaliens"
        wp rewrite structure '/%postname%/' --hard --allow-root

    fi

    # `wp core install` comme un dump réécrit laissent home et siteurl à des
    # valeurs qui peuvent diverger des constantes. On écrit via $wpdb :
    # `wp option update` serait un no-op, update_option() comparant la nouvelle
    # valeur à celle que renvoie la constante.
    wp eval '
        global $wpdb;
        $wpdb->update($wpdb->options, ["option_value" => WP_HOME], ["option_name" => "home"]);
        $wpdb->update($wpdb->options, ["option_value" => WP_SITEURL], ["option_name" => "siteurl"]);
    ' --allow-root
fi

# -----------------------------------------------------------------------------
# 4) Cache pleine page (W3 Total Cache).
#
#    ⚠️ On ne DÉSACTIVE PAS l'extension : en se désactivant, elle SUPPRIME ses
#    deux greffons `website/app/advanced-cache.php` et `object-cache.php`, qui
#    sont VERSIONNÉS. Lancer la pile laissait donc deux suppressions dans
#    l'arbre de travail, prêtes à être committées par mégarde — constaté le
#    26/09/2026.
#
#    C'est WP_CACHE qui commande, et cela suffit : WordPress ne charge
#    `advanced-cache.php` que si la constante vaut true (voir wp-config.php).
#    Le cache pleine page est donc inerte en local sans toucher à l'extension.
# -----------------------------------------------------------------------------
if [ "${WP_CACHE}" = "true" ]; then
    echo "==> [init] WP_CACHE=true : cache pleine page ACTIF."
    wp plugin activate w3-total-cache --allow-root >/dev/null 2>&1 \
        || echo "!!! [init] Activation de W3 Total Cache échouée (ignoré)."
else
    echo "==> [init] WP_CACHE=false : cache pleine page inerte (extension laissée en l'état)."
    wp cache flush --allow-root >/dev/null 2>&1 || true
fi

# -----------------------------------------------------------------------------
# 5) Thème. La BASE fait foi — sauf quand elle désigne un thème qui n'est plus
#    livré. C'est le cas du dump de production, qui active encore `vilva` :
#    WordPress sert alors une page VIDE en 200, un symptôme qui ne ressemble pas
#    à une erreur. On se rabat donc sur WP_DEFAULT_THEME (wp-config.php), et on
#    le dit fort.
# -----------------------------------------------------------------------------
ACTIVE_THEME="$(wp eval 'echo get_option("stylesheet");' --allow-root 2>/dev/null | tr -d '[:space:]' || true)"
DEFAULT_THEME="$(wp eval 'echo defined("WP_DEFAULT_THEME") ? WP_DEFAULT_THEME : "";' --allow-root 2>/dev/null | tr -d '[:space:]' || true)"

if [ -n "$ACTIVE_THEME" ] && wp theme is-installed "$ACTIVE_THEME" --allow-root 2>/dev/null; then
    echo "==> [init] Thème actif : ${ACTIVE_THEME}"
elif [ -n "$DEFAULT_THEME" ] && wp theme is-installed "$DEFAULT_THEME" --allow-root 2>/dev/null; then
    echo "!!! [init] La base désigne le thème '${ACTIVE_THEME:-aucun}', absent du disque."
    wp theme activate "$DEFAULT_THEME" --allow-root
    echo "==> [init] Rabattu sur WP_DEFAULT_THEME : ${DEFAULT_THEME}"
else
    echo "!!! [init] Aucun thème utilisable ('${ACTIVE_THEME:-aucun}' en base, '${DEFAULT_THEME:-aucun}' par défaut) : le site sortira une page vide."
fi

echo "==> [init] Initialisation terminée."
echo "    Front : ${WP_HOME}"
echo "    Admin : ${WP_HOME}/wordpress-core/wp-admin"
