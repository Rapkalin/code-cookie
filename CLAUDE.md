# CLAUDE.md — Règles du projet (code-cookie)

> **Instructions pour tout assistant Claude travaillant sur ce dépôt.**
> En cas de doute : **demander, ne pas improviser.**
>
> Ces règles sont reprises du dépôt `~/Sites/lcds`, à la demande explicite de
> Raphael (26/09/2026). Ce qui n'existe pas encore ici est signalé comme tel :
> ne pas prétendre l'appliquer avant de l'avoir mis en place.

---

## 1. Méthode de travail

- **Avancer par validation** : proposer et expliquer les choix, valider **avant**
  d'écrire du code pour toute tâche non triviale.
- **Simplicité d'abord (KISS)** : la solution la plus simple qui fonctionne.
- **Minimiser les sorties** : réponses concises, pas de remplissage.

### Challenger la demande — passe obligatoire avant de répondre

Avant toute réponse impliquant une décision technique, se poser ces questions et
**dire ce qui coince** :

1. **Est-ce que ça résout le vrai problème ?** La demande décrit parfois une
   solution ; vérifier qu'elle traite bien la cause.
2. **Est-ce que ça tient dans 6 mois ?** Duplication, valeur en dur, couplage,
   configuration qui devra être maintenue à deux endroits.
3. **Qu'est-ce que ça casse ?** Sécurité, données, compatibilité, environnements
   autres que celui sous les yeux.
4. **Y a-t-il plus simple ?** Si oui, le proposer — même si la demande est claire.
5. **Sur quoi je m'avance ?** Version d'un paquet, comportement d'une API,
   existence d'une option : **vérifier plutôt qu'affirmer**.

### Prouver, et déclarer ce qui reste supposé

Toute affirmation technique doit reposer sur un **élément concret** produit dans
la session : sortie de commande, mesure, source lue, test exécuté. « Ça devrait
marcher » n'est pas une vérification.

- **Vérifier dans les deux sens.** Affirmer qu'un correctif était nécessaire
  suppose d'avoir constaté l'échec sans lui. Une règle ajoutée « au cas où » se
  révèle parfois inutile, voire nuisible.
- **Un test qui ne peut pas échouer ne vaut rien.** Après en avoir écrit un,
  casser volontairement le code qu'il surveille et vérifier qu'il passe au rouge.
- **Ne jamais conclure à une absence depuis un outil non exhaustif.**
- **Déclarer explicitement ce qui reste supposé**, en fin de réponse, plutôt que
  de le noyer dans une formulation prudente. L'utilisateur doit pouvoir repérer
  d'un coup d'œil ce qui est prouvé et ce qui ne l'est pas.

Règles de conduite :

- **Signaler ≠ bloquer.** Exposer la réserve en une ou deux phrases, puis
  **faire le travail demandé**. L'utilisateur tranche.
- **Une demande reformulée ou répétée vaut décision** : appliquer sans réouvrir
  le débat.
- **Ne pas fabriquer d'objection.** Quand la demande est bonne, le dire en une
  ligne et exécuter. Un challenge systématique et creux ne vaut rien.
- **Distinguer le fait de l'avis** : « ça ne marchera pas parce que X » (vérifié)
  n'est pas « je préférerais Y » (préférence).

### Ne jamais livrer du shell non exécuté

Leçon payée trois fois sur LCDS : `~` non développé depuis une variable,
`ln -nsf` créant le lien **dans** un dossier existant, exclusion `rsync` non
ancrée supprimant un dossier du cœur WordPress. Chacune a mis un environnement à
terre pendant des heures.

- Avant de livrer un script destiné au serveur, le **simuler** (bac à sable dans
  `/tmp` ou dans le conteneur) et **montrer la sortie**.
- Les commandes à coller sont **courtes, une par ligne**, sans heredoc, sans
  `$(...)`, sans longue chaîne base64 : le terminal de Raphael replie les
  collages longs et les casse.
- Commencer par la commande la plus courte qui discrimine, pas par le script de
  cinquante lignes.

---

## 2. Qualité de code

- **Le projet tourne dans Docker** (mis en place le 26/09/2026). Toute commande
  PHP / Composer / WP-CLI passe par `docker compose exec php …`. **Ne jamais
  supposer que PHP est disponible sur l'hôte** — vérifié : il ne l'est pas de
  manière fiable.
- **Types natifs** sur tout paramètre, retour et propriété du code écrit ici. Le
  code hérité n'est pas à réécrire pour autant : le convertir quand on le touche.
- **Comparaisons : variable à gauche**, jamais de conditions Yoda
  (`$field === null`, pas `null === $field`).
- **Commentaires : le moins possible.** Le code se documente par ses noms.
  N'écrire un commentaire que si, sans lui, un dev **casserait** le code —
  typiquement un piège d'API externe non déductible à la lecture. Interdits :
  reformuler un nom de fonction, décrire la ligne suivante, rappeler une règle
  générale, raconter l'historique de la demande.
- **DRY** : une valeur = un seul endroit.
- **Nommage** : pas de variables de moins de 3 caractères ; booléens préfixés
  `is` / `has` / `can`.
- ⚠️ **Pas encore de gate de qualité ici** : ni Pint, ni PHPCS, ni PHPStan, ni
  tests, contrairement à LCDS. Ne pas annoncer un `composer check` qui n'existe
  pas. À mettre en place quand Raphael l'arbitrera.

---

## 3. Langue

- **Code en anglais** : noms de variables, de fonctions, commentaires, docblocks.
- **Documentation et échanges en français.**
- **Chaînes affichées traduites** via `__()` / `_e()`, text-domain **littéral**
  (sinon `wp i18n make-pot` casse).

---

## 4. Sécurité — non négociable

- **Toute entrée est hostile.** Sanitiser en entrée (`sanitize_*`), échapper en
  sortie (`esc_html`, `esc_attr`, `esc_url`), préparer les requêtes SQL
  (`$wpdb->prepare`).
- **Tout endpoint AJAX / REST public** vérifie un **nonce** et les capabilities.
- **Jamais de secret en dur** dans le code : tout passe par le `.env`. Un secret
  committé = un secret à révoquer, même après suppression du commit.
- ⚠️ **Dette connue** : `website/wp-config.php` porte les huit clés et sels
  d'authentification **en dur**, aux valeurs d'exemple de WordPress
  (`put your unique phrase here`). À déplacer dans le `.env` et à régénérer
  **avant** toute remise en ligne.
- **Uploads** : allow-list d'extensions **et** vérification du type MIME réel.
- **Ne pas modifier `website/wordpress-core/`** ni le contenu des extensions
  installées par Composer : ce sont des dépendances, écrasées à la prochaine
  mise à jour.
- **Avant de committer un `.htaccess`**, relire le `git diff` : les extensions le
  régénèrent et y injectent parfois des chemins absolus propres à une machine.
- **Jamais de secret ni d'extension sous licence dans le dépôt** : ils vivent
  dans `shared/` côté serveur, qui n'est ni versionné ni écrasé par un
  déploiement.

---

## 5. Structure

- Le **docroot est `website/`**, pas la racine : `.env`, `composer.json` et
  `scripts/` restent hors de portée du serveur web.
- Le cœur WordPress est installé par Composer dans `website/wordpress-core/`
  (non versionné), le `wp-content` est remplacé par `website/app/`.
- Les extensions gratuites et le **thème parent** (`newsmatic`) viennent de
  **wpackagist** et ne sont pas versionnés (voir `.gitignore`). Sont versionnés
  ce qui porte le travail maison : l'extension
  `website/app/plugins/info-bulle-block` et le thème enfant
  `website/app/themes/newsmatic-child`.
- **Le thème de référence est déclaré une seule fois**, par `WP_DEFAULT_THEME`
  dans `website/wp-config.php`. `bin/init.sh` y réconcilie le thème que la base
  déclare : ne pas réintroduire de nom de thème en dur ailleurs.

---

## 6. Git

- **Une branche par ticket** : `feature/xxx`. Bases : `main` (prod),
  `develop` (préprod).
- **Messages de commit** : `[{ticket}][{TYPE}] message` — `FEAT` ou `FIX`.
- **Ne jamais committer ni pousser sans demande explicite de l'utilisateur.**
- **Ne jamais committer le `.env`** ni un fichier contenant un secret.

---

## 7. Documentation

Tenir à jour la documentation quand une décision de socle change (sécurité,
déploiement, Docker, CI). Une règle non documentée sera contournée au prochain
sprint.
