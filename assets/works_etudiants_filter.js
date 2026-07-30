/**
 * Filtre dynamiquement le champ « Étudiants » du formulaire Works (création
 * et édition) pour ne proposer que les stagiaires de la formation choisie.
 * Le filtrage serveur (WorksCrudController::configureFields) ne s'applique
 * qu'au rendu initial en édition (une fois le Work déjà persisté avec sa
 * formation) ; ce script le complète en trois temps :
 * - au chargement du formulaire, car le <select> Formation a toujours une
 *   valeur pré-sélectionnée (pas de placeholder vide) même en création,
 *   sans qu'aucun événement "change" ne se déclenche ;
 * - à chaque changement explicite de formation par l'utilisateur ;
 * - dès que le widget TomSelect d'EasyAdmin (activé par défaut sur les
 *   AssociationField, cf. `data-ea-widget="ea-autocomplete"`) se connecte
 *   sur le champ Étudiants, car TomSelect remplace visuellement le <select>
 *   natif : modifier directement ses <option> ne suffit pas, il faut passer
 *   par l'API `element.tomselect` pour que l'affichage se mette à jour.
 */

function refreshEtudiants(formationId, usersSelect, clearIfEmpty) {
    if (!formationId) {
        if (clearIfEmpty) updateUsersOptions(usersSelect, []);
        return;
    }

    fetch(`/admin/works/etudiants-formation/${formationId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then(r => r.json())
        .then(etudiants => updateUsersOptions(usersSelect, etudiants))
        .catch(err => console.error('[CF2m] Filtrage étudiants par formation:', err));
}

function initEtudiantsFilter() {
    const formationSelect = document.getElementById('Works_formation');
    const usersSelect = document.getElementById('Works_users');
    if (!formationSelect || !usersSelect) return;

    refreshEtudiants(formationSelect.value, usersSelect, false);
}

document.addEventListener('DOMContentLoaded', initEtudiantsFilter);
document.addEventListener('turbo:load', initEtudiantsFilter);
document.addEventListener('turbo:frame-load', initEtudiantsFilter);

// Event delegation sur document : survit aux navigations Turbo
document.addEventListener('change', function (event) {
    if (event.target.id !== 'Works_formation') return;

    const usersSelect = document.getElementById('Works_users');
    if (!usersSelect) return;

    refreshEtudiants(event.target.value, usersSelect, true);
});

// TomSelect s'initialise de façon asynchrone (contrôleur EasyAdmin séparé) : si le
// champ Formation a déjà une valeur au moment où TomSelect se connecte sur le champ
// Étudiants, on resynchronise pour ne pas dépendre de l'ordre d'exécution des scripts.
document.addEventListener('ea.autocomplete.connect', function (event) {
    if (event.target.id !== 'Works_users') return;

    const formationSelect = document.getElementById('Works_formation');
    if (!formationSelect) return;

    refreshEtudiants(formationSelect.value, event.target, false);
});

function updateUsersOptions(select, etudiants) {
    const tomSelect = select.tomselect;

    if (tomSelect) {
        const previouslySelected = new Set(tomSelect.items);

        tomSelect.clear(true);
        tomSelect.clearOptions();
        etudiants.forEach(({ id, text }) => {
            tomSelect.addOption({ value: String(id), text });
        });

        const stillValidSelection = etudiants
            .map(({ id }) => String(id))
            .filter(value => previouslySelected.has(value));
        tomSelect.setValue(stillValidSelection, true);
        tomSelect.refreshOptions(false);

        return;
    }

    // Repli si le widget TomSelect n'est pas (encore) initialisé sur ce champ
    const previouslySelected = new Set(
        Array.from(select.selectedOptions).map(option => option.value)
    );

    select.innerHTML = '';

    etudiants.forEach(({ id, text }) => {
        const option = document.createElement('option');
        option.value = String(id);
        option.textContent = text;
        option.selected = previouslySelected.has(String(id));
        select.appendChild(option);
    });
}
