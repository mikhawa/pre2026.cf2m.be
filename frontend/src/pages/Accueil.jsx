import { useEffect, useState } from 'react';
import { apiGet } from '../api/client';

/**
 * Page d'accueil provisoire du squelette : vérifie que l'API Symfony répond.
 * Sera remplacée par le portage de l'en-tête, du hero et du pied de page (étape 2).
 */
export default function Accueil() {
    const [statut, setStatut] = useState('chargement');

    useEffect(() => {
        const controleur = new AbortController();

        apiGet('/statut', { signal: controleur.signal })
            .then((donnees) => setStatut(donnees.statut))
            .catch((erreur) => {
                if (erreur.name !== 'AbortError') {
                    setStatut('injoignable');
                }
            });

        return () => controleur.abort();
    }, []);

    return (
        <main>
            <h1>CF2m — SPA React</h1>
            <p>Backend : {statut}</p>
        </main>
    );
}
