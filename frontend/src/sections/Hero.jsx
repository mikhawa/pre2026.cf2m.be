/**
 * Hero de la page d'accueil (maquette Figma « Header + Hero »).
 * Props construites par App\Service\Layout\LayoutPropsBuilder::hero().
 * Le <h1> et le bouton existent aussi dans le contenu de repli Twig
 * (home/_hero_fallback.html.twig) pour le SEO et l'affichage avant React.
 */
export default function Hero({ title, subtitle, stats, tagline, cta, background, portrait }) {
    return (
        <section className="cf2m-home-hero">
            <picture className="cf2m-home-hero__bg">
                <source media="(max-width: 767.98px)" srcSet={background.mobile} />
                <img src={background.desktop} alt="" fetchPriority="high" />
            </picture>

            <div className="container cf2m-home-hero__inner">
                <div className="row align-items-center g-4">
                    <div className="col-lg-7">
                        <h1 className="cf2m-home-hero__title">
                            {title}
                            <span className="cf2m-home-hero__subtitle">{subtitle}</span>
                        </h1>

                        <dl className="cf2m-home-hero__stats">
                            {stats.map((stat) => (
                                <div key={stat.label} className="cf2m-home-hero__stat">
                                    <dt className="cf2m-home-hero__stat-value">{stat.value}</dt>
                                    <dd className="cf2m-home-hero__stat-label">{stat.label}</dd>
                                </div>
                            ))}
                        </dl>

                        <p className="cf2m-home-hero__tagline">{tagline}</p>

                        <a href={cta.url} className="cf2m-home-hero__cta">{cta.label}</a>
                    </div>

                    <div className="col-lg-5 d-none d-lg-flex justify-content-center">
                        <div className="cf2m-home-hero__portrait">
                            <img className="cf2m-home-hero__ring" src={portrait.ring} alt="" aria-hidden="true" />
                            <img className="cf2m-home-hero__photo" src={portrait.src} alt={portrait.alt} width="340" height="340" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
