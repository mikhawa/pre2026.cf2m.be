/**
 * Logo CF2m : version blanche (thème sombre) et bleue (thème clair),
 * la bascule est faite en CSS via [data-theme] (cf. .cf2m-logo-img--light).
 */
export default function Logo({ logo, size }) {
    return (
        <>
            <img src={logo.dark} alt="Logo CF2m" width={size} height={size} className="cf2m-logo-img cf2m-logo-img--dark" />
            <img src={logo.light} alt="" width={size} height={size} className="cf2m-logo-img cf2m-logo-img--light" aria-hidden="true" />
        </>
    );
}
