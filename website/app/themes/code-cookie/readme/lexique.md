# Le lexique de l'interface

Les mots de l'interface jouent sur les deux sens de « cookie » — le gâteau et le
traceur web.

**La règle de dosage : un jeu de mots par surface, jamais deux.** Un écran qui
blague trois fois ne blague plus. Et aucun mot portant une action ne doit
demander un décodage.

| Élément WordPress | Chez nous | Pourquoi |
|---|---|---|
| Catégories `articles-*` | Les persistants | Article de fond : il reste après la session |
| Catégories `astuces-*` | Les sessions | Astuce courte, consommée sur place |
| Fil d'Ariane | Les miettes | « Breadcrumb » est déjà la métaphore |
| Étiquettes | Les pépites | Ce qu'on trouve dedans |
| Pagination | La fournée suivante | Dit ce qu'elle fait sans dire « page 2 » |
| Recherche vide | Pas une miette. | Court, suivi d'une porte de sortie |
| Erreur 404 | Ce cookie a expiré. | Expiration web et gâteau mangé, à la fois |
| Commentaires | Laisser une miette | — |

## Écartés, et pourquoi

- **« Temps de cuisson »** pour le temps de lecture : un lecteur pressé cherche
  un chiffre, pas une devinette.
- **« Aucun cookie tiers »** en pied de page : ce serait faux tant que des
  extensions appellent `stats.wp.com` ou `cdn-cookieyes.com`.
