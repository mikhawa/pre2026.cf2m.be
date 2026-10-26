import Logo from '../components/Logo';

/**
 * Pied de page du site (en attente de la maquette Figma : reprend le design actuel).
 * Props construites par App\Service\Layout\LayoutPropsBuilder::footer().
 */
export default function Footer({ homeUrl, logo, tagline, activities, year }) {
    return (
        <div className="container">
            <div className="row align-items-center">
                <div className="col-md-4 mb-3 mb-md-0 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                    <a href={homeUrl} aria-label="CF2m — Accueil">
                        <Logo logo={logo} size={50} />
                    </a>
                    <span className="text-muted">{tagline}</span>
                </div>

                <div className="col-md-4 mb-3 mb-md-0">
                    {activities.length > 0 && (
                        <>
                            <p className="text-muted small fw-semibold mb-1 text-uppercase">Nos activités</p>
                            <ul className="list-unstyled mb-0">
                                {activities.map((activity) => (
                                    <li key={activity.url}>
                                        <a className="text-muted text-decoration-none cf2m-footer-link small" href={activity.url}>
                                            {activity.label}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </>
                    )}
                </div>

                <div className="col-md-4 text-center text-md-end">
                    <small className="text-muted">&copy; {year} CF2m — Tous droits réservés</small>
                </div>
            </div>
        </div>
    );
}
