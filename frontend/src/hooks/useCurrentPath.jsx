import { useEffect, useState } from 'react';

/**
 * Chemin courant (location.pathname), mis à jour à chaque navigation Turbo.
 * Indispensable pour les îlots « permanents » (en-tête) qui ne sont pas
 * remontés entre deux pages.
 */
export default function useCurrentPath() {
    const [path, setPath] = useState(() => window.location.pathname);

    useEffect(() => {
        const update = () => setPath(window.location.pathname);
        document.addEventListener('turbo:load', update);

        return () => document.removeEventListener('turbo:load', update);
    }, []);

    return path;
}
