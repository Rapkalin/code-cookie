#!/usr/bin/env sh
#
# Bump the release version.
#
# composer.json is the only place the version is written: the theme reads it at
# runtime for the footer and for asset cache busting. This script refuses to
# bump a version that the changelog does not document — that is the step which
# actually drifts.
#
#   bin/release.sh 2.1.0
#
# It does not commit and does not tag: it prints the commands to run.

set -eu

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
MANIFEST="$ROOT/composer.json"
CHANGELOG="$ROOT/CHANGELOG.md"
VERSION="${1:-}"

if [ -z "$VERSION" ]; then
    echo "usage: bin/release.sh <x.y.z>" >&2
    exit 2
fi

if ! printf '%s' "$VERSION" | grep -Eq '^[0-9]+\.[0-9]+\.[0-9]+$'; then
    echo "refus : '$VERSION' n'est pas une version semver x.y.z" >&2
    exit 1
fi

if [ ! -f "$MANIFEST" ] || [ ! -f "$CHANGELOG" ]; then
    echo "refus : composer.json ou CHANGELOG.md introuvable sous $ROOT" >&2
    exit 1
fi

if ! grep -Fq "## [$VERSION]" "$CHANGELOG"; then
    echo "refus : CHANGELOG.md n'a aucune section '## [$VERSION]'." >&2
    echo "        Documenter la version avant de la poser." >&2
    exit 1
fi

OCCURRENCES="$(grep -c '"version":' "$MANIFEST")"
if [ "$OCCURRENCES" -ne 1 ]; then
    echo "refus : '\"version\":' apparait $OCCURRENCES fois dans composer.json, 1 attendue." >&2
    exit 1
fi

CURRENT="$(sed -n 's/.*"version": *"\([^"]*\)".*/\1/p' "$MANIFEST")"

sed -i.bak "s/\"version\": *\"[^\"]*\"/\"version\": \"$VERSION\"/" "$MANIFEST"
rm -f "$MANIFEST.bak"

echo "composer.json : $CURRENT -> $VERSION"
echo
echo "Il reste a committer, puis a poser le tag :"
echo "  git add composer.json CHANGELOG.md"
echo "  git commit -m \"[INIT][FEAT] release $VERSION\""
echo "  git tag $VERSION"
echo "  git push --tags"
