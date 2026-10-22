import Icon from './Icon';

/**
 * Entrée de menu déroulante (Bootstrap dropdown, via data-bs-toggle).
 * Props : { id, label, items: [{label, url, recruiting?}], emptyLabel, footerLink?, external?, active }
 */
export default function NavDropdown({ id, label, items, emptyLabel, footerLink, external, active }) {
    return (
        <li className="nav-item dropdown">
            <a
                className={`nav-link dropdown-toggle${active ? ' active' : ''}`}
                href="#"
                id={id}
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                {label}
            </a>
            <ul className="dropdown-menu cf2m-dropdown" aria-labelledby={id}>
                {items.length === 0 && (
                    <li>
                        <span className="dropdown-item-text text-muted fst-italic">{emptyLabel}</span>
                    </li>
                )}
                {items.map((item) => (
                    <li key={item.url}>
                        <a
                            className={`dropdown-item cf2m-dropdown-item${item.recruiting ? ' cf2m-dropdown-item--recruiting' : ''}`}
                            href={item.url}
                        >
                            <span className="cf2m-dropdown-arrow">›</span>
                            {item.label}
                            {item.recruiting && <span className="badge bg-warning text-dark ms-1 cf2m-badge-recruiting">Recrutement</span>}
                        </a>
                    </li>
                ))}
                {external && (
                    <li>
                        <a className="dropdown-item cf2m-dropdown-item" href={external.url} target="_blank" rel="noopener noreferrer" title={external.title}>
                            <span className="cf2m-dropdown-arrow">›</span>
                            {external.label} <Icon name="external" size={11} className="cf2m-external-icon" />
                        </a>
                    </li>
                )}
                {footerLink && items.length > 0 && (
                    <>
                        <li><hr className="dropdown-divider cf2m-dropdown-divider" /></li>
                        <li><a className="dropdown-item cf2m-dropdown-all" href={footerLink.url}>{footerLink.label}</a></li>
                    </>
                )}
            </ul>
        </li>
    );
}
