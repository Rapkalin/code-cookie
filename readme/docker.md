# Environnement de développement Docker

Le projet tourne **entièrement dans Docker**. Aucun PHP, Composer, MySQL ni
WP-CLI n'est requis sur la machine hôte, et le vHost Apache décrit dans
l'ancien `readme.md` n'a plus lieu d'être.

## Démarrage

```
cp .env.example .env
docker compose up -d
```

Au premier démarrage, l'entrypoint enchaîne : `composer install` (cœur
WordPress, extensions, thème) → génération des sels → attente de la base →
`bin/init.sh`. Compter quelques minutes, uniquement la première fois.

| Service     | URL                                             |
|-------------|-------------------------------------------------|
| Front       | http://localhost:8030                           |
| Admin       | http://localhost:8030/wordpress-core/wp-admin   |
| phpMyAdmin  | http://localhost:8031                           |
| Mailpit     | http://localhost:8035                           |
| MySQL       | `localhost:3308` (hors conteneur)               |

Ces ports sont pilotés par le `.env` et ont été choisis libres sur le poste de
Raphael : `8020`/`8021`/`8025` appartiennent à LCDS, `3306` et `3307` à MySQL
local et à LCDS.

## Ce que fait `bin/init.sh`

Idempotent : relancer le conteneur ne réécrit jamais une base existante.

1. Génère les sels encore à `generateme` dans le `.env`.
2. Ouvre en écriture `uploads`, `cache`, `languages`, `w3tc-config`.
3. Base de données, dans cet ordre :
   - WordPress déjà installé → **on ne touche à rien** ;
   - un `.sql` (ou `.sql.gz`) attend dans `exports/` → il est importé, puis les
     URLs de production sont réécrites vers `WP_HOME` ;
   - sinon → installation neuve, permaliens, activation du seul thème présent.
4. Laisse W3 Total Cache tel que la base le déclare ; c'est `WP_CACHE` qui
   commande le cache pleine page (inerte en local).
5. Affiche le thème actif **lu dans la base** — c'est elle qui fait foi, pas
   `composer.json`.

## Restaurer le site dans son état de production

Les deux sources vivent hors du dépôt, chez Raphael :

| Quoi | Où | Ce que ça porte |
|------|----|-----------------|
| Base | `~/Documents/Perso/codecookie/BDD/dbs12940112.sql` | dump IONOS du 26/09/2026 — `codecookie.fr`, 32 articles publiés |
| Médias | `~/Documents/Perso/codecookie/_sources/uploads/` | 3427 fichiers, 725 Mo |

```
cp ~/Documents/Perso/codecookie/BDD/dbs12940112.sql exports/
rsync -a ~/Documents/Perso/codecookie/_sources/uploads/ website/app/uploads/
docker compose down -v && docker compose up -d
```

Les deux destinations sont ignorées par Git : rien de tout cela n'entre dans le
dépôt.

⚠️ **Le dump active encore `vilva`, thème qui n'est plus livré.** Le passage à
vilva n'était pas voulu : le site tourne sur `newsmatic-child`, rétabli depuis
l'historique Git (commit `51f4c3f^`, dernier état versionné). `bin/init.sh`
rattrape donc l'écart à chaque import et se rabat sur `WP_DEFAULT_THEME`
(`wp-config.php`), en le disant dans le journal. Cette réconciliation deviendra
inutile le jour où la base de production sera basculée à son tour — **c'est à
faire au moment de la remise en ligne**, sinon la production servira une page
blanche en 200.

⚠️ **Un média est définitivement perdu** :
`2023/11/explain-code-me-boolean-check-new-jpg.webp`, l'image à la une de
« Astuce PHP // Traiter correctement la validation des booléens », donc la
première vignette du carrousel d'accueil. Absente du dossier des médias comme de
`preprod-backup.zip` (dont les 3427 fichiers sont identiques). Les 256 autres
pièces jointes sont présentes.

⚠️ **`wp-consent-api` est déclarée active en base sans exister nulle part** : ni
dans `composer.json`, ni dans la sauvegarde de code de 2024, et elle n'a laissé
aucune option en base. Elle n'a donc jamais tourné sur le site — WordPress la
saute en silence, en local comme en production. Ne pas l'ajouter « pour faire
propre » : ce serait changer le comportement du site, pas le restaurer.

## Importer une autre base

Déposer le dump dans `exports/` puis repartir d'une base vierge :

```
docker compose down -v
docker compose up -d
docker compose logs -f php
```

`down -v` supprime le volume MySQL : c'est ce qui permet à l'import de jouer,
puisqu'il est sauté dès que WordPress est installé. Le dump le plus récent du
dossier est retenu (`ls -t`), `.sql` comme `.sql.gz`.

Les médias vont dans `website/app/uploads` — un simple dossier du dépôt, monté
dans le conteneur : les y copier suffit.

## Commandes courantes

```
docker compose exec php wp <commande> --allow-root
docker compose exec php composer <commande>
docker compose logs -f php
docker compose down          # arrête, garde la base
docker compose down -v       # arrête ET supprime la base
```

### ⚠️ `wp db …` ne fonctionne pas sur cette pile

`wp db import`, `wp db export` et `wp db query` échouent tous sur
`TLS/SSL error: self-signed certificate in certificate chain`. Ce n'est pas
rattrapable par configuration : ces sous-commandes délèguent au client mariadb
avec leur propre `--defaults-file`, qui écarte le `/root/.my.cnf` de l'image —
et l'option `--skip-ssl` n'atteint pas la requête interne que WP-CLI lance
avant la commande. **Tout le reste de WP-CLI fonctionne** (`wp search-replace`,
`wp eval`, `wp plugin`, `wp core`…) : ces commandes passent par PHP, pas par le
client en ligne de commande.

Appeler le client directement, qui lui lit `/root/.my.cnf` :

```
# Exporter
docker compose exec php bash -lc \
  'mariadb-dump -h db -u codecookie -pcodecookie --no-tablespaces codecookie > exports/export.sql'

# Importer
docker compose exec php bash -lc 'mariadb -h db -u codecookie -pcodecookie codecookie < exports/export.sql'

# Interroger
docker compose exec php bash -lc \
  'mariadb -h db -u codecookie -pcodecookie -N -e "SELECT option_value FROM wp_options WHERE option_name=\"home\";" codecookie'
```

`--no-tablespaces` n'est pas décoratif : sans lui, `mariadb-dump` réclame le
privilège `PROCESS`, que l'utilisateur applicatif n'a pas.

C'est aussi ce que fait `bin/init.sh` pour importer un dump.

## Mails

Aucun mail ne sort de la machine : `sendmail_path` pointe vers `msmtp`, qui
parle à Mailpit. C'est un **garde-fou**, pas un confort — un dump de production
contient de vraies adresses d'abonnés.

Le greffon `website/app/mu-plugins/code-cookie-local-mail.php` corrige
l'expéditeur : hors production, WordPress fabrique `wordpress@localhost`, que
PHPMailer refuse comme invalide, et **tout `wp_mail()` échoue** avant même
d'atteindre le relais. Le greffon ne s'active pas en production.

## Version de PHP

L'image est construite en **PHP 8.3**, valeur par défaut de l'argument
`PHP_VERSION` (surchargeable dans le `.env`). C'est la version qui couvre le
`composer.lock` actuel, figé sur WordPress 6.7.1. À monter en 8.4 une fois le
cœur et les extensions mis à jour — et après avoir vérifié le PHP réellement
servi par l'hébergeur, qui n'est pas forcément celui de sa ligne de commande.

## Pièges déjà rencontrés

- **`composer install` peut sortir en succès en ayant laissé des paquets de
  côté.** Constaté au premier démarrage : le cœur WordPress et Jetpack étaient
  absents alors que `website/vendor` existait. L'entrypoint vérifie donc
  `wordpress-core/wp-settings.php` **après** l'installation et s'arrête net avec
  un message plutôt que de lancer un WordPress vide.
- **Désactiver W3 Total Cache SUPPRIME deux fichiers versionnés.** L'extension
  retire ses greffons `website/app/advanced-cache.php` et `object-cache.php` en
  se désactivant : la pile laissait alors deux suppressions dans l'arbre de
  travail, prêtes à partir dans un commit. `bin/init.sh` ne la désactive donc
  plus — `WP_CACHE=false` suffit à rendre le cache pleine page inerte, puisque
  WordPress ne charge `advanced-cache.php` que si la constante vaut `true`.
- **Un thème manquant donne un `200` avec un corps VIDE**, symptôme qui ne
  ressemble pas à une erreur. Le parent `newsmatic` vient de Composer et n'est
  pas versionné (voir `.gitignore`) ; l'enfant `newsmatic-child` **l'est**, car
  il porte toute la personnalisation. `bin/init.sh` réconcilie le thème de la
  base avec `WP_DEFAULT_THEME` à chaque démarrage.
