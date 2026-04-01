<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Page;
use App\Entity\User;
use App\Service\RevisionService;
use Doctrine\Bundle\FixturesBundle\Attribute\AsFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixtures de production — données réelles uniquement.
 *
 * Lancement : php bin/console doctrine:fixtures:load --group=prod --env=prod
 *
 * Variables d'environnement requises (dans .env.local sur le VPS) :
 *   PROD_ADMIN_EMAIL     ex: mikhawa@cf2m.be
 *   PROD_ADMIN_USERNAME  ex: Mikhawa
 *   PROD_ADMIN_PASSWORD  ex: un-mot-de-passe-fort
 */
#[AsFixture(groups: ['prod'])]
class ProdFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly RevisionService $revisionService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // ── Super administrateur ─────────────────────────────────────────────────

        $email    = (string) (getenv('PROD_ADMIN_EMAIL')    ?: throw new \RuntimeException('PROD_ADMIN_EMAIL non défini dans .env.local'));
        $username = (string) (getenv('PROD_ADMIN_USERNAME') ?: throw new \RuntimeException('PROD_ADMIN_USERNAME non défini dans .env.local'));
        $password = (string) (getenv('PROD_ADMIN_PASSWORD') ?: throw new \RuntimeException('PROD_ADMIN_PASSWORD non défini dans .env.local'));

        $admin = new User();
        $admin
            ->setEmail($email)
            ->setUserName($username)
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setStatus(1)
            ->setPassword($this->hasher->hashPassword($admin, $password));

        $manager->persist($admin);
        $manager->flush();

        // ── Pages CMS ────────────────────────────────────────────────────────────

        $pages = [];

        $pages[] = $this->createPage(
            manager: $manager,
            author:  $admin,
            title:   'À propos de notre centre',
            slug:    'about',
            content: <<<HTML
                <!-- TODO : remplacer par le vrai contenu HTML de la page "À propos" -->
                <h2>Le Centre de Formation CF2m</h2>
                <p>Contenu à remplir.</p>
                HTML,
        );

        $pages[] = $this->createPage(
            manager: $manager,
            author:  $admin,
            title:   'RGPD et confidentialité',
            slug:    'rgpd',
            content: <<<HTML
                <!-- TODO : remplacer par le vrai texte légal RGPD -->
                <h2>Protection des données personnelles</h2>
                <p>Contenu à remplir.</p>
                HTML,
        );

        $pages[] = $this->createPage(
            manager: $manager,
            author:  $admin,
            title:   'Nos valeurs et notre mission',
            slug:    'nos-valeurs-et-notre-mission',
            content: <<<HTML
                <!-- TODO : remplacer par le vrai contenu HTML de la page "Valeurs" -->
                <h2>Notre mission</h2>
                <p>Contenu à remplir.</p>
                HTML,
        );

        $manager->flush();

        // Révisions initiales des pages
        foreach ($pages as $page) {
            $this->revisionService->createRevision($page, $admin, true, isCreation: true);
        }
        $manager->flush();

        // ── Formations ───────────────────────────────────────────────────────────

        $formations = [];

        $formations[] = $this->createFormation(
            manager: $manager,
            author:  $admin,
            title:   'Aventure digitale',
            slug:    'aventure-digitale',
            descriptionCourte: 'TODO : description courte Aventure digitale (max 800 caractères)',
            description: <<<HTML
                <!-- TODO : remplacer par la vraie description HTML de la formation "Aventure digitale" -->
                <p>Contenu à remplir.</p>
                HTML,
            colorPrimary:   '#25365f',
            colorSecondary: '#00589a',
        );

        $formations[] = $this->createFormation(
            manager: $manager,
            author:  $admin,
            title:   'Animateur multimédia',
            slug:    'animateur-multimedia',
            descriptionCourte: 'TODO : description courte Animateur multimédia (max 800 caractères)',
            description: <<<HTML
                <!-- TODO : remplacer par la vraie description HTML de la formation "Animateur multimédia" -->
                <p>Contenu à remplir.</p>
                HTML,
            colorPrimary:   '#25365f',
            colorSecondary: '#00589a',
        );

        $formations[] = $this->createFormation(
            manager: $manager,
            author:  $admin,
            title:   'Technicien PC & réseaux',
            slug:    'technicien-reseaux',
            descriptionCourte: 'TODO : description courte Technicien PC & réseaux (max 800 caractères)',
            description: <<<HTML
                <!-- TODO : remplacer par la vraie description HTML de la formation "Technicien PC & réseaux" -->
                <p>Contenu à remplir.</p>
                HTML,
            colorPrimary:   '#25365f',
            colorSecondary: '#00589a',
        );

        $formations[] = $this->createFormation(
            manager: $manager,
            author:  $admin,
            title:   'Digital Designer',
            slug:    'digital-designer',
            descriptionCourte: 'TODO : description courte Digital Designer (max 800 caractères)',
            description: <<<HTML
                <!-- TODO : remplacer par la vraie description HTML de la formation "Digital Designer" -->
                <p>Contenu à remplir.</p>
                HTML,
            colorPrimary:   '#25365f',
            colorSecondary: '#00589a',
        );

        $formations[] = $this->createFormation(
            manager: $manager,
            author:  $admin,
            title:   'Web Developer Full Stack',
            slug:    'developpeur-web',
            descriptionCourte: 'TODO : description courte Web Developer Full Stack (max 800 caractères)',
            description: <<<HTML
                <!-- TODO : remplacer par la vraie description HTML de la formation "Web Developer Full Stack" -->
                <p>Contenu à remplir.</p>
                HTML,
            colorPrimary:   '#25365f',
            colorSecondary: '#00589a',
        );

        $formations[] = $this->createFormation(
            manager: $manager,
            author:  $admin,
            title:   'Chèques TIC',
            slug:    'cheques-tic',
            descriptionCourte: 'TODO : description courte Chèques TIC (max 800 caractères)',
            description: <<<HTML
                <!-- TODO : remplacer par la vraie description HTML de la formation "Chèques TIC" -->
                <p>Contenu à remplir.</p>
                HTML,
            colorPrimary:   '#25365f',
            colorSecondary: '#00589a',
        );

        $manager->flush();
    }

    // ── Méthodes privées ─────────────────────────────────────────────────────────

    private function createPage(
        ObjectManager $manager,
        User $author,
        string $title,
        string $slug,
        string $content,
    ): Page {
        $page = new Page();
        $page
            ->setTitle($title)
            ->setSlug($slug)
            ->setContent(trim($content))
            ->setStatus('published')
            ->setPublishedAt(new \DateTimeImmutable())
            ->addUser($author);

        $manager->persist($page);

        return $page;
    }

    private function createFormation(
        ObjectManager $manager,
        User $author,
        string $title,
        string $slug,
        string $descriptionCourte,
        string $description,
        string $colorPrimary,
        string $colorSecondary,
    ): Formation {
        $formation = new Formation();
        $formation
            ->setTitle($title)
            ->setSlug($slug)
            ->setDescriptionCourte($descriptionCourte)
            ->setDescription(trim($description))
            ->setStatus('published')
            ->setPublishedAt(new \DateTimeImmutable())
            ->setCreatedBy($author)
            ->setColorPrimary($colorPrimary)
            ->setColorSecondary($colorSecondary);

        $manager->persist($formation);

        return $formation;
    }
}
