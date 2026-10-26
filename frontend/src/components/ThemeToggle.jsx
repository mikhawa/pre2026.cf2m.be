import Icon from './Icon';

/**
 * Bouton de bascule clair/sombre.
 * Le comportement reste dans le contrôleur Stimulus « theme »
 * (assets/controllers/theme_controller.js), qui se connecte aussi aux
 * éléments rendus par React : une seule source de vérité pour le thème.
 */
export default function ThemeToggle({ className = '' }) {
    return (
        <button
            type="button"
            className={`cf2m-theme-toggle ${className}`}
            data-controller="theme"
            data-action="click->theme#toggle"
            aria-label="Changer de thème"
        >
            <Icon name="sun" className="cf2m-theme-sun" />
            <Icon name="moon" className="cf2m-theme-moon" />
        </button>
    );
}
