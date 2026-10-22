/**
 * Appel à l'API JSON de Symfony (même origine : le cookie de session part avec la requête).
 * Lève une erreur si la réponse n'est pas un succès HTTP.
 */
export async function apiGet(chemin, { signal } = {}) {
    const reponse = await fetch(`/api${chemin}`, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
        signal,
    });

    if (!reponse.ok) {
        throw new Error(`Erreur API ${reponse.status} sur ${chemin}`);
    }

    return reponse.json();
}
