import { Controller } from '@hotwired/stimulus';

/* data-controller="burger" — ouvre/ferme le menu mobile (<1060px). */
export default class extends Controller {
    static targets = ['menu'];

    toggle() {
        this.menuTarget.classList.toggle('is-open');
    }

    close() {
        this.menuTarget.classList.remove('is-open');
    }
}
