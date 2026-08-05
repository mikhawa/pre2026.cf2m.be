import { Controller } from '@hotwired/stimulus';

/* data-controller="theme" — toggle dark/light + direction visuelle, persisté en localStorage.
   Applique les attributs sur <html> : data-theme="dark|light", data-dir="a|b". */
export default class extends Controller {
    static targets = ['icon'];

    connect() {
        const theme = localStorage.getItem('cf2m-theme') || document.documentElement.dataset.theme || 'dark';
        const dir = localStorage.getItem('cf2m-dir') || document.documentElement.dataset.dir || 'a';
        this.apply(theme, dir);
    }

    toggle() {
        const next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
        this.apply(next, document.documentElement.dataset.dir || 'a');
    }

    toggleDir() {
        const next = document.documentElement.dataset.dir === 'a' ? 'b' : 'a';
        this.apply(document.documentElement.dataset.theme || 'dark', next);
    }

    apply(theme, dir) {
        document.documentElement.dataset.theme = theme;
        document.documentElement.dataset.dir = dir;
        localStorage.setItem('cf2m-theme', theme);
        localStorage.setItem('cf2m-dir', dir);
        if (this.hasIconTarget) this.iconTarget.textContent = theme === 'light' ? '☾' : '☀';
    }
}
