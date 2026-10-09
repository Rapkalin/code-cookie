# theme.json

Version 3. Il porte la palette, les familles de police, l'échelle de tailles et
les espacements, et il **bride l'éditeur** :

- `color.custom: false` et `color.defaultPalette: false` — un rédacteur ne peut
  pas sortir de la charte depuis le sélecteur de couleur ;
- `typography.customFontSize: false` et `spacing.customSpacingSize: false` —
  mêmes raisons ;
- `layout.contentSize` 760px, `wideSize` 1140px.

## La palette

| Jeton | Valeur | Rôle |
|---|---|---|
| `ardoise` | `#1E2731` | Fond général — repris de l'ancien design |
| `plaque` | `#2E3844` | Cartes, en-tête — repris de l'ancien design |
| `pastille` | `#3A4654` | Boutons neutres |
| `lait` | `#F2F5F7` | Texte (13,80:1) |
| `buee` | `#A9B6C2` | Texte secondaire (7,31:1) |
| `caramel` | `#FBB04D` | Accent principal (8,22:1) |
| `orange` | `#EB8518` | Backend (5,68:1) |
| `terre` | `#E67D5E` | Corrigé : l'ancien `#D95F3D` tombait à 4,07 |
| `menthe` | `#6FBF9B` | Cache, écoresponsable (6,91:1) |

Les valeurs sémantiques (fond, texte, bordures) ne sont **pas** dans
`theme.json` : elles basculent avec le mode d'affichage et vivent donc en
variables CSS dans `src/scss/_tokens.scss`.
