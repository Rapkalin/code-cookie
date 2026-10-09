# Styles (SCSS)

Les styles sont écrits en SCSS dans `src/scss/` et compilés par
[Dart Sass](https://sass-lang.com/dart-sass) seul — pas de `@wordpress/scripts`.

```
npm install        # une fois
npm run build:css  # compile src/scss/style.scss -> style.css
npm run watch:css  # idem, en continu
```

## Pourquoi `style.css` est versionné

Aucune étape de build ne compile le SCSS au déploiement : WordPress sert
directement le `style.css` du dossier du thème, et la CI ne porte pas encore de
job Node. Sans le fichier compilé au dépôt, le site déployé n'aurait aucun style.

> 🔧 **À faire ensuite** : ajouter `npm ci && npm run build:css` au workflow de
> déploiement, puis retirer `style.css` du versionnement pour n'avoir plus qu'une
> seule source de vérité. L'en-tête de thème vit dans `style.scss` (`/*! … */`)
> et survit à la compilation, donc l'ignorer ne pose pas de problème d'identité.

## Jetons sémantiques et bascule de thème

`_tokens.scss` déclare des variables CSS sur `:root` pour le mode **sombre**, qui
est le défaut, et les redéclare sous `[data-scheme="light"]`.

L'attribut est posé **par le navigateur**, jamais par PHP. Une page servie depuis
le cache pleine page porte le balisage par défaut, et le script d'amorçage la
corrige avant le premier rendu. Poser l'attribut côté serveur figerait le choix
d'un visiteur dans la copie mise en cache de tout le monde.

## La règle des parfums

Les quatre couleurs de famille (caramel, orange, terre, menthe) sont des
**remplissages**, pas des couleurs de texte : sur fond clair elles tombent entre
1,63 et 2,49 de contraste, très loin du seuil de 4,5. Chaque parfum porte donc
un second jeton `--cc-*-ink`, qui bascule vers une variante cuite en mode clair.
