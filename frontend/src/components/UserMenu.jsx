import Icon from './Icon';

/**
 * Menu de l'utilisateur connecté. Les liens présents (administration, badge des
 * révisions en attente) sont décidés côté serveur (LayoutPropsBuilder) : aucun
 * test de rôle ici. La déconnexion recharge toute la page (data-turbo="false")
 * pour que l'en-tête, permanent entre les navigations Turbo, soit remis à jour.
 */
export default function UserMenu({ user }) {
    return (
        <div className="dropdown">
            <button
                className="cf2m-user-dropdown-toggle dropdown-toggle"
                type="button"
                id="dropdownUser"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span className="cf2m-user-avatar-icon"><Icon name="user" /></span>
                {user.name}
            </button>
            <ul className="dropdown-menu dropdown-menu-end cf2m-dropdown cf2m-user-dropdown" aria-labelledby="dropdownUser">
                <li>
                    <a className="dropdown-item cf2m-dropdown-item" href={user.profileUrl}>
                        <span className="cf2m-dropdown-arrow">›</span>
                        Mon profil
                    </a>
                </li>
                {user.adminUrl && (
                    <li>
                        <a className="dropdown-item cf2m-dropdown-item" href={user.adminUrl} data-turbo="false">
                            <span className="cf2m-dropdown-arrow">›</span>
                            Administration
                            {user.pendingRevisions && (
                                <span className="badge rounded-pill ms-1 cf2m-badge-pending" aria-label={`${user.pendingRevisions} révisions en attente`}>
                                    {user.pendingRevisions}
                                </span>
                            )}
                        </a>
                    </li>
                )}
                <li><hr className="dropdown-divider cf2m-dropdown-divider" /></li>
                <li>
                    <a className="dropdown-item cf2m-dropdown-all" href={user.logoutUrl} data-turbo="false">Déconnexion</a>
                </li>
            </ul>
        </div>
    );
}
