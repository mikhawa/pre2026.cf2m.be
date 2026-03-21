import { Controller } from '@hotwired/stimulus';

/**
 * Controller de basculement dark/light mode.
 * Stocke le choix dans localStorage et l'applique sur <html data-theme="...">.
 */
export default class extends Controller {
    connect() {
        const saved = localStorage.getItem('cf2m-theme') || 'dark';
        this.applyTheme(saved);
    }

    toggle() {
        const current = document.documentElement.dataset.theme || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        this.applyTheme(next);
        localStorage.setItem('cf2m-theme', next);
    }

    applyTheme(theme) {
        document.documentElement.dataset.theme = theme;
        const isDark = theme === 'dark';
        this.element.querySelector('.cf2m-theme-sun').hidden  = isDark;
        this.element.querySelector('.cf2m-theme-moon').hidden = !isDark;
        this.element.setAttribute(
            'aria-label',
            isDark ? 'Passer en mode clair' : 'Passer en mode sombre'
        );
        this.element.setAttribute('title',
            isDark ? 'Mode clair' : 'Mode sombre'
        );
    }
}
