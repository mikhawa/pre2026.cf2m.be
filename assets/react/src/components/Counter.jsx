/**
 * Composant réutilisable d'exemple : un bouton compteur.
 * Les composants de ce dossier ne sont pas appelés depuis Twig,
 * ils sont importés par les îlots (dossier islands/).
 */
export default function Counter({ value, onIncrement }) {
    return (
        <button type="button" className="btn btn-primary btn-sm" onClick={onIncrement}>
            Cliqué {value} fois
        </button>
    );
}
