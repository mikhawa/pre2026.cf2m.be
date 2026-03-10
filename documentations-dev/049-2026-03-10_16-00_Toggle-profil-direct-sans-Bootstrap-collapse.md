# 049 — Toggle profil direct (sans Bootstrap collapse)

**Date** : 2026-03-10 16:00
**Ticket** : correction UX page profil

## Fichiers modifiés
- `templates/profil/index.html.twig`
- `assets/styles/app.css`

## Raison
Le composant Bootstrap collapse animait la transition entre les deux blocs, laissant les deux blocs visibles simultanément pendant l'animation. L'utilisateur demandait un remplacement instantané.

## Changements

### Bouton toggle
- Supprimé : `data-bs-toggle="collapse"`, `data-bs-target`, `aria-expanded`, `aria-controls`, les deux `<span>` label
- Ajouté : `id="cf2m-profile-toggle"`, texte simple "Modifier mon profil"

### Wrapper formulaire
- `class="collapse"` → `class="d-none"`

### JavaScript
- Remplacé les listeners Bootstrap collapse (`show.bs.collapse`, `hide.bs.collapse`) par un `addEventListener('click')` direct
- `showEdit()` : `profileView.classList.add('d-none')` + `editForm.classList.remove('d-none')` + `toggleBtn.textContent = 'Mon profil'`
- `showProfile()` : inverse
- localStorage conservé pour persistance entre rechargements

### CSS
- Supprimé les règles `aria-expanded` devenues obsolètes
