import { Link } from 'react-router';

export default function PageIntrouvable() {
    return (
        <main>
            <h1>Page introuvable</h1>
            <p>
                <Link to="/">Retour à l'accueil</Link>
            </p>
        </main>
    );
}
