# 200 — Remplacement des images hero par celles du thème claude.ai/design

**Modèle utilisé** : Sonnet

**Justification** : analyse d'un export binaire (extraction d'assets base64 embarqués dans
un fichier HTML) + remplacement de fichiers image + ajustement d'un attribut de template —
complexité modérée, pas d'architecture ni de logique métier.

## Contexte

Suite à la tâche 199 (fond des pages intérieures), l'utilisateur a précisé vouloir que les
images de fond et « l'image de l'avant » (portrait) du canvas claude.ai/design **remplacent**
les images actuelles du site, pas seulement s'en inspirer côté couleurs.

## Analyse

L'export `datas/canvas-claude/` contient 3 variantes HTML du même canvas. Les deux fichiers
`.dc.html` référencent l'image de fond hero via une URL Unsplash externe
(`images.unsplash.com/photo-1517048676732...`), simple placeholder pendant l'édition. Le
fichier `CF2M Site (hors ligne).html` (export « offline », 681 Ko) embarque en revanche une
carte JSON de 20 assets en base64 (polices woff2, SVG, JS, et 2 photos JPEG) — c'est cette
carte qui contient les images réellement utilisées, décodées et extraites localement.

Les 2 photos identifiées :
- `7db492ad-9269-4c9c-852d-33ec7ec45029` (1600×1067, 172 Ko) → `--hero-img`, fond hero
- `d5463d3d-d810-46f0-b539-5c11dfd8909f` (600×900, 68 Ko) → portrait circulaire
  (`alt="Stagiaire CF2m"` dans le HTML source)

**Point d'attention signalé et confirmé par l'utilisateur** : ces deux photos sont des
photos stock génériques (la photo de fond est littéralement le placeholder Unsplash de
l'outil de design ; le portrait est un mannequin stock, pas un vrai stagiaire du CF2m —
contrairement à l'actuel `hero-portrait.jpg`, qui provient d'une vraie photo du CF2m
d'après `datas/resize_hero.php`). L'utilisateur a explicitement confirmé vouloir les deux
remplacements malgré cela.

## Fichiers modifiés

- `public/images/hero-bg.jpg` — remplacé (2,7 Mo, 4032×3024 → 172 Ko, 1600×1067)
- `public/images/hero-portrait.jpg` — remplacé (229 Ko, 800×800 → 68 Ko, 600×900)
- `templates/home/index.html.twig` (ligne 64) — `alt="Étudiante CF2m"` → `alt="Stagiaire CF2m"`
  (le nouveau portrait montre un homme, l'ancien texte alternatif n'était plus exact)

## Hors scope (volontairement non touché)

- `public/images/hero-bg.webp`, `hero-bg-mobile.webp`, `hero-portrait.webp` : fichiers
  orphelins, non référencés dans `templates/`, `assets/` ni `src/` (vérifié par recherche
  globale) — laissés tels quels (anciennes photos), aucun impact visible sur le site.
- `public/images/hero-groupe.jpg` (photo de groupe, slide alterné + og:image) : non demandé,
  reste la vraie photo CF2m existante.
- `datas/resize_hero.php` (script générateur historique de l'ancien portrait) : non modifié.

## Résultat

Fichiers remplacés et vérifiés (`file` confirme les nouvelles dimensions/poids). Pas de
vérification visuelle en navigateur (les conteneurs Docker de l'utilisateur montent le
checkout principal, pas ce worktree) — à confirmer par l'utilisateur après merge.
