#!/usr/bin/env bash
# =============================================================================
# Entrypoint du conteneur PHP/Apache.
# Séquence : dépendances Composer -> .env -> attente base -> init WP -> apache.
# Idempotent : peut être relancé sans casser une installation existante.
# Le processus final (apache2-foreground) vient du CMD du Dockerfile.
# =============================================================================
set -e

APP_DIR="/var/www/html"
cd "$APP_DIR"

# Le .env vit à la racine du dépôt en local, dans le docroot sur le serveur
# (website/.env, lien vers shared/.env). shared/ est un concept SERVEUR : il n'a
# pas à exister sur un poste de dev.
if [ -f "$APP_DIR/website/.env" ]; then
    ENV_FILE="$APP_DIR/website/.env"
else
    ENV_FILE="$APP_DIR/.env"
fi

echo "==> [entrypoint] Démarrage du conteneur PHP/Apache"

# -----------------------------------------------------------------------------
# 1) Fichier .env (copié depuis .env.example s'il manque). AVANT composer :
#    wp-config.php exige un .env, et le moindre script PHP le chargerait.
# -----------------------------------------------------------------------------
if [ ! -f "$ENV_FILE" ]; then
    echo "==> [entrypoint] .env absent : copie depuis .env.example"
    cp "$APP_DIR/.env.example" "$ENV_FILE"
fi

# -----------------------------------------------------------------------------
# 2) Dépendances Composer. Le vendor-dir du projet est website/vendor (et non
#    vendor/ à la racine) — voir composer.json, config.vendor-dir.
#
#    La sonde porte sur wp-settings.php, pas sur l'existence de website/vendor :
#    une première extraction a déjà échoué sur deux paquets (le cœur et jetpack)
#    en laissant website/vendor en place. L'entrypoint avait alors enchaîné sur
#    l'initialisation d'un WordPress absent, et le conteneur bouclait en
#    redémarrages. Constaté le 26/09/2026 sur ce poste.
# -----------------------------------------------------------------------------
AUTOLOAD="$APP_DIR/website/vendor/autoload.php"
WP_SETTINGS="$APP_DIR/website/wordpress-core/wp-settings.php"

if [ ! -f "$AUTOLOAD" ] || [ ! -f "$WP_SETTINGS" ]; then
    echo "==> [entrypoint] Dépendances incomplètes : composer install"
    composer install --no-interaction --prefer-dist --no-progress
else
    echo "==> [entrypoint] Dépendances en place : composer install ignoré"
fi

# Contrôle APRÈS coup : l'extraction en parallèle peut laisser un paquet de côté.
# Sans ce garde-fou, l'échec ne se voit qu'à la page blanche du navigateur.
if [ ! -f "$AUTOLOAD" ] || [ ! -f "$WP_SETTINGS" ]; then
    echo "!!! [entrypoint] Installation Composer incomplète."
    [ -f "$AUTOLOAD" ]    || echo "!!! manquant : website/vendor/autoload.php"
    [ -f "$WP_SETTINGS" ] || echo "!!! manquant : website/wordpress-core/wp-settings.php"
    echo "!!! Relancer à la main :  docker compose run --rm --entrypoint bash php -lc 'composer install'"
    exit 1
fi

# -----------------------------------------------------------------------------
# 3) Les variables fournies par compose ou la plateforme PRIMENT sur le .env.
#    On les y recopie pour que tout le monde lise les mêmes valeurs : PHP
#    (phpdotenv) ET bin/init.sh (shell).
# -----------------------------------------------------------------------------
update_env() {
    local key="$1" val="$2" file="$ENV_FILE"
    # Délimiteur sed '|' : absent des URLs et identifiants usuels.
    if grep -qE "^${key}=" "$file"; then
        sed -i "s|^${key}=.*|${key}='${val}'|" "$file"
    else
        echo "${key}='${val}'" >> "$file"
    fi
}

for KEY in WP_ENV WP_HOME DATABASE_NAME DATABASE_USER DATABASE_PASSWORD DATABASE_HOST \
           DATABASE_PREFIX WP_TITLE WP_ADMIN_USER WP_ADMIN_PASSWORD WP_ADMIN_EMAIL \
           WP_CACHE DISABLE_WP_CRON \
           AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY \
           AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT; do
    # Indirection bash : ${!KEY} = valeur de la variable nommée par $KEY.
    if [ -n "${!KEY+x}" ] && [ -n "${!KEY}" ]; then
        update_env "$KEY" "${!KEY}"
        echo "==> [entrypoint] .env : ${KEY} surchargé depuis l'environnement"
    fi
done

# -----------------------------------------------------------------------------
# 4) Charger le .env (désormais cohérent) pour les sous-processus.
#    `set -a` les exporte automatiquement.
# -----------------------------------------------------------------------------
set -a
# shellcheck disable=SC1090
. "$ENV_FILE"
set +a

# -----------------------------------------------------------------------------
# 5) Attente de la base (max ~60 s).
#    On teste via mysqli, exactement le chemin qu'emprunte WordPress : cela gère
#    caching_sha2_password, contrairement au client mysqladmin.
# -----------------------------------------------------------------------------
export DATABASE_HOST="${DATABASE_HOST:-db}"
export DATABASE_USER="${DATABASE_USER:-codecookie}"
export DATABASE_PASSWORD="${DATABASE_PASSWORD:-codecookie}"
echo "==> [entrypoint] Attente de la base sur '${DATABASE_HOST}'..."

ATTEMPTS=0
MAX_ATTEMPTS=30   # 30 x 2s = 60s
until php -r '$c=@mysqli_connect(getenv("DATABASE_HOST"),getenv("DATABASE_USER"),getenv("DATABASE_PASSWORD")); exit($c ? 0 : 1);' >/dev/null 2>&1; do
    ATTEMPTS=$((ATTEMPTS + 1))
    if [ "$ATTEMPTS" -ge "$MAX_ATTEMPTS" ]; then
        echo "!!! [entrypoint] La base n'a pas répondu après ~60 s. Abandon."
        exit 1
    fi
    echo "    ... base pas encore prête (tentative ${ATTEMPTS}/${MAX_ATTEMPTS})"
    sleep 2
done
echo "==> [entrypoint] Base joignable."

# -----------------------------------------------------------------------------
# 6) Initialisation WordPress (idempotente)
# -----------------------------------------------------------------------------
echo "==> [entrypoint] Exécution de bin/init.sh"
bash "$APP_DIR/bin/init.sh"

# -----------------------------------------------------------------------------
# 7) Démarrage du processus principal (apache via CMD)
# -----------------------------------------------------------------------------
echo "==> [entrypoint] Initialisation terminée. Démarrage : $*"
exec "$@"
