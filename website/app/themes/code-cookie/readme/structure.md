# Structure du dossier

```
code-cookie/
├── style.css         GÉNÉRÉ — sortie compilée du SCSS (en-tête WP + styles)
├── theme.json        Jetons de design, version 3, et bridage de l'éditeur
├── functions.php     Point d'entrée : uniquement des require_once de inc/
├── package.json      sass en devDependency, build:css / watch:css
├── header.php footer.php
├── index.php         Repli : accueil et archives sans gabarit plus précis
├── single.php page.php archive.php search.php 404.php comments.php
├── searchform.php
├── inc/
│   ├── enums/
│   │   ├── CookieKind.php     Les deux familles éditoriales (persistant / session)
│   │   └── MenuLocation.php   Emplacements de menu
│   ├── assets.php             Chargement de la feuille et du script
│   ├── breadcrumb.php         « Les miettes »
│   ├── legacy-filters.php     Règles de requête reprises de l'ancien thème
│   ├── pagination.php         « La fournée suivante »
│   ├── scheme.php             Switch clair/sombre, appliqué côté client
│   ├── security.php           Durcissement et allègement de l'en-tête
│   └── setup.php              Supports, traductions, emplacements de menu
├── components/card.php
├── assets/js/scheme.js
└── src/scss/        sources SCSS — voir readme/styles.md
```

**Pour étendre le thème** : ajouter un `inc/xxx.php` et son `require_once` dans
`functions.php`, en gardant l'ordre alphabétique. Chaque fichier ne fait que
déclarer des fonctions et accrocher des hooks : l'ordre de chargement ne porte
aucune dépendance.
