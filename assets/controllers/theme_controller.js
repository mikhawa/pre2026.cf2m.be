import { Controller } from '@hotwired/stimulus';

/**
 * Toggle dark/light mode.
 * Les icônes et textes sont pilotés par CSS via [data-theme] sur <html>.
 */
export default class extends Controller {
    connect() {
        const saved = localStorage.getItem('cf2m-theme') || 'dark';
        document.documentElement.dataset.theme = saved;
    }

    toggle() {
        const current = document.documentElement.dataset.theme || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = next;
        localStorage.setItem('cf2m-theme', next);
    }
}
