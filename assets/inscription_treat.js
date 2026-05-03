/**
 * Mise à jour en temps réel du statut traitement des inscriptions.
 * Utilise l'event delegation sur le toggle "treat" de la liste EasyAdmin.
 * Attend 600ms que le PATCH EA soit terminé, puis rafraîchit la ligne et le badge.
 */

// Event delegation sur document : survit aux navigations Turbo
document.addEventListener('change', function (event) {
    const checkbox = event.target;

    if (checkbox.type !== 'checkbox') return;

    const treatCell = checkbox.closest('td[data-column="treat"]');
    if (!treatCell) return;

    const row = checkbox.closest('tr[data-id]');
    if (!row) return;

    const entityId = row.dataset.id;
    if (!entityId) return;

    // Attend que le PATCH EasyAdmin soit terminé avant de lire les données
    setTimeout(() => refreshRow(entityId), 600);
});

function refreshRow(entityId) {
    fetch(`/admin/inscription/${entityId}/traitement-info`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then(r => r.json())
        .then(data => {
            updateCells(entityId, data);
            updateBadge(data.untreatedCount);
        })
        .catch(err => console.error('[CF2m] Traitement inscription:', err));
}

function updateCells(entityId, data) {
    const row = document.querySelector(`tr[data-id="${entityId}"]`);
    if (!row) return;

    const treatAtCell = row.querySelector('td[data-column="treatAt"]');
    const treatByCell = row.querySelector('td[data-column="treatBy"]');

    if (treatAtCell) {
        treatAtCell.innerHTML = data.treatAt
            ? `<time datetime="${data.treatAtIso}">${data.treatAt}</time>`
            : '';
    }

    if (treatByCell) {
        treatByCell.innerHTML = data.treatBy ? escapeHtml(data.treatBy) : '';
    }
}

function updateBadge(count) {
    const link = document.querySelector('a.menu-item-contents[href*="InscriptionCrudController"]');
    if (!link) return;

    const label = link.querySelector('.menu-item-label');
    if (!label) return;

    let badge = label.querySelector('.menu-item-badge');

    if (count > 0) {
        if (badge) {
            badge.textContent = count;
        } else {
            badge = document.createElement('span');
            badge.className = 'menu-item-badge rounded-pill badge bg-danger';
            badge.textContent = count;
            label.appendChild(badge);
        }
    } else {
        badge?.remove();
    }
}

function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
