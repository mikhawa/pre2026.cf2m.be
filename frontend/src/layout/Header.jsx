import { useEffect, useRef } from 'react';
import Logo from '../components/Logo';
import NavDropdown from '../components/NavDropdown';
import ThemeToggle from '../components/ThemeToggle';
import UserMenu from '../components/UserMenu';
import useCurrentPath from '../hooks/useCurrentPath';

function isActive(item, path) {
    return item.exact ? path === item.match : path.startsWith(item.match);
}

/**
 * En-tête du site (maquette Figma « Header + Hero »).
 * Monté une seule fois par chargement complet : l'élément <header> est
 * data-turbo-permanent et l'îlot « permanent » (voir base.html.twig).
 * Props construites par App\Service\Layout\LayoutPropsBuilder::header().
 */
export default function Header({ homeUrl, logo, tagline, nav, user, loginUrl }) {
    const path = useCurrentPath();
    const collapseRef = useRef(null);
    const togglerRef = useRef(null);

    // Le menu mobile reste ouvert entre deux pages (en-tête permanent) : on le referme à chaque navigation.
    useEffect(() => {
        collapseRef.current?.classList.remove('show');
        togglerRef.current?.classList.add('collapsed');
        togglerRef.current?.setAttribute('aria-expanded', 'false');
    }, [path]);

    return (
        <nav className="navbar navbar-expand-lg cf2m-site-header__nav" aria-label="Navigation principale">
            <div className="container">
                <a className="navbar-brand cf2m-site-header__brand" href={homeUrl} aria-label="CF2m — Accueil">
                    <Logo logo={logo} size={95} />
                    <span className="cf2m-site-header__tagline">{tagline}</span>
                </a>

                <ThemeToggle className="d-lg-none ms-auto me-2" />

                <button
                    ref={togglerRef}
                    className="navbar-toggler collapsed border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#cf2m-main-nav"
                    aria-controls="cf2m-main-nav"
                    aria-expanded="false"
                    aria-label="Afficher/masquer le menu"
                >
                    <span className="navbar-toggler-icon" />
                </button>

                <div ref={collapseRef} className="collapse navbar-collapse" id="cf2m-main-nav">
                    <ul className="navbar-nav cf2m-site-header__links">
                        {nav.map((item, index) => {
                            const active = isActive(item, path);

                            return item.type === 'dropdown' ? (
                                <NavDropdown key={item.label} id={`cf2m-nav-dropdown-${index}`} active={active} {...item} />
                            ) : (
                                <li className="nav-item" key={item.label}>
                                    <a className={`nav-link${active ? ' active' : ''}`} href={item.url} aria-current={active ? 'page' : undefined}>
                                        {item.label}
                                    </a>
                                </li>
                            );
                        })}
                    </ul>

                    <div className="cf2m-site-header__actions">
                        <ThemeToggle className="d-none d-lg-inline-flex" />
                        {user ? (
                            <UserMenu user={user} />
                        ) : (
                            <a className="cf2m-btn-connexion" href={loginUrl}>Connexion</a>
                        )}
                    </div>
                </div>
            </div>
        </nav>
    );
}
