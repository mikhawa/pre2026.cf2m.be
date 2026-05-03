/**
 * Mise à jour en temps réel du statut traitement des inscriptions.
 * Intercepte le PATCH d'EasyAdmin après sa résolution (pas de race condition)
 * puis rafraîchit les colonnes "Traitée le" / "Traitée par" et le badge du menu.
 */

const _originalFetch = window.fetch.bind(window);

window.fetch = function (url, options) {
    const promise = _originalFetch(url, options);

    if (
        typeof url === 'string' &&
        options?.method === 'PATCH' &&
        url.includes('InscriptionCrudController')
    ) {
        // On attend que le PATCH EA soit résolu avant d'appeler notre endpoint
        promise
            .then(response => {
                if (!response.ok) return;
                const params = new URLSearchParams(url.split('?')[1] ?? '');
                const entityId = params.get('entityId');
                if (entityId) refreshRow(entityId);
            })
            .catch(() => {});
    }

    return promise;
};

function refreshRow(entityId) {
    _originalFetch(`/admin/inscription/${entityId}/traitement-info`, {
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
