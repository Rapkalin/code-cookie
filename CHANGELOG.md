# Changelog

Toutes les modifications notables de ce projet sont documentées dans ce fichier.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/), et le
projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

La version qui fait foi est le champ `version` de `composer.json`. Elle est
affichée en pied de page sur **tous** les environnements, et doit correspondre au
tag Git de la mise en production. `bin/release.sh` tient les trois en phase.

## [Unreleased]

## [2.0.0] - 2026-10-09

Refonte complète du site autour de l'identité Code Cookie. **Version majeure :
l'ancien thème est supprimé et le contenu de certains articles a été réécrit.**

### Added
- **Thème maison `code-cookie`**, hybride : gabarits PHP classiques, design
  piloté par `theme.json` (v3) qui bride l'éditeur. Transposé du gabarit
  `steamulo-theme` de game-france, à l'échelle d'un blog.
- **Mode clair / sombre** avec bascule dans l'en-tête, sombre par défaut. La
  préférence est posée côté client, donc compatible avec le cache pleine page.
- **Contribution ACF Pro** : page de réglages du thème, accroche et mention de
  crédit du pied de page en Local JSON versionné (`acf-json/`).
- **Les miettes** (fil d'Ariane), **les parfums** (une couleur par sujet,
  attribuée depuis l'enum `Flavour`), **temps de lecture** calculé.
- **Icône de marque** servie par le thème, sans passer par la médiathèque.
- **Versionnement semver**, ce changelog, et `bin/release.sh`.

### Changed
- Palette reprise des couleurs de l'ancien design (`#1E2731`, `#2E3844`,
  `#556068`, fond clair `#F0F1F2`), contrastes recalculés. Le terracotta
  `#D95F3D` est remonté à `#E67D5E` : il tombait à 4,07 sur le fond ardoise.
- Typographie **Baloo 2 / Karla / JetBrains Mono** en remplacement d'Inter,
  Jost et Roboto.
- **Contenus migrés** hors des extensions supprimées : 16 `jetpack/tiled-gallery`
  vers `core/gallery`, 11 `aab/accordion-item` vers `core/details`, sur
  8 articles. Les ancres des accordéons sont conservées.

### Removed
- Thèmes `newsmatic` et `newsmatic-child`, et la dépendance
  `wpackagist-theme/newsmatic`.
- Logo et favicon de l'ancienne marque (`explain-code-to-me`).

### Fixed
- **Galeries cassées** : les images du Forum PHP étaient servies depuis
  `i0/i1/i2.wp.com/explain.code-to.me`, le CDN Photon sur un domaine éteint.
  6 articles concernés, 50 images rien que sur l'un d'eux.
- Couverture de carte : le sélecteur CSS ne correspondait à aucune classe émise,
  les couleurs de parfum n'étaient jamais appliquées.
- Pastilles d'article : la catégorie retenue était un niveau de difficulté
  (« Débutant ») au lieu du sujet (« Backend »).
- Liens : `theme.json` imposait un soulignement partout. Il est retiré, sauf
  dans le corps des articles où la couleur seule ne suffit pas (contraste de
  1,68 avec le texte, seuil WCAG à 3).
- Bascule d'affichage : l'icône était figée sur le cookie croqué, quel que soit
  le mode.

[Unreleased]: https://github.com/Rapkalin/explain-code/compare/2.0.0...HEAD
[2.0.0]: https://github.com/Rapkalin/explain-code/compare/1.0.3...2.0.0
