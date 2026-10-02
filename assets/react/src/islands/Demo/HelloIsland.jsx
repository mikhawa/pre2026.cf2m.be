import { useState } from 'react';
import Counter from '../../components/Counter.js';

/**
 * Îlot de démonstration : vérifie que la chaîne Twig → Stimulus → React
 * fonctionne (props, état, rendu, navigation Turbo).
 * Affiché uniquement en environnement dev sur la page d'accueil.
 */
export default function HelloIsland({ name }) {
    const [count, setCount] = useState(0);

    return (
        <div className="alert alert-info my-4" role="status">
            <p className="mb-2">
                Îlot React actif — bonjour <strong>{name}</strong> !
            </p>
            <Counter value={count} onIncrement={() => setCount(count + 1)} />
        </div>
    );
}
