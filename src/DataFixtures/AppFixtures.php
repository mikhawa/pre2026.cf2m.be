<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\CommentFactory;
use App\Factory\ContactMessageFactory;
use App\Factory\FormationFactory;
use App\Factory\InscriptionFactory;
use App\Factory\PageFactory;
use App\Factory\PartenaireFactory;
use App\Factory\RatingFactory;
use App\Factory\UserFactory;
use App\Factory\WorksFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use faker\Factory as Faker;

class AppFixtures extends Fixture
{



    public function load(ObjectManager $manager): void
    {
        // Initialiser Faker pour générer du contenu réaliste
        $faker = Faker::create('fr_FR');

        // ── Super administrateur de test ────────────────────────────────────────
        $usersManuel = [];
        $usersManuel[] = UserFactory::createOne([
            'email'         => 'mikhawa@cf2m.be',
            'userName'      => 'Mikhawa',
            'roles'         => ['ROLE_SUPER_ADMIN'],
            'status'        => 1,
            'plainPassword' => '123mikhawa',
        ]);
        // ── Administrateur de test ────────────────────────────────────────
        $usersManuel[] = UserFactory::createOne([
            'email'         => 'thejoe@cf2m.be',
            'userName'      => 'TheJoe',
            'roles'         => ['ROLE_ADMIN'],
            'status'        => 1,
            'plainPassword' => '123joe',
        ]);
        // ── Formateur de test ────────────────────────────────────────
        $usersManuel[] = UserFactory::createOne([
            'email'         => 'piet@cf2m.be',
            'userName'      => 'ThePiet',
            'roles'         => ['ROLE_FORMATEUR'],
            'status'        => 1,
            'plainPassword' => '123piet',
        ]);


        // ── Fakers Utilisateurs ────────────────────────────────────────────────
        $admins     = UserFactory::createMany(2, ['roles' => ['ROLE_ADMIN']]);
        $formateurs = UserFactory::createMany(8, fn () => ['roles' => ['ROLE_FORMATEUR']]);
        $etudiants  = UserFactory::createMany(35);

        // Regrouper les utilisateurs manuels, admins et formateurs pour les associer à des formations
        $adminsAndFormateurs = [...$usersManuel, ...$admins, ...$formateurs];

        // Regrouper tous les utilisateurs pour les associer à des commentaires, notations, etc.
        $tousLesUsers = [...$usersManuel, ...$admins, ...$formateurs, ...$etudiants];

        // ── Partenaires ─────────────────────────────────────────────────
        PartenaireFactory::createMany(6);
        PartenaireFactory::createMany(2, fn () => ['active' => false]);

        // ── Pages CMS ───────────────────────────────────────────────────
        $pagesManuel = [];
        $pagesManuel[] = PageFactory::createOne([
            'title'       => 'A propos de notre centre',
            'slug'        => 'about',
            'content'     => '<p>' . $faker->realText(300) . '</p><p>' . $faker->realText(300) . '</p><p>' . $faker->realText(200) . '</p>',
            'status'      => 'published',
            'publishedAt' => new \DateTimeImmutable('-3 month'),
        ]);
        $pagesManuel[] = PageFactory::createOne([
            'title'       => 'RGPD et confidentialité',
            'slug'        => 'rgpd',
            'content'     => '<p>' . $faker->realText(300) . '</p><p>' . $faker->realText(300) . '</p><p>' . $faker->realText(200) . '</p>',
            'status'      => 'published',
            'publishedAt' => new \DateTimeImmutable('-2 months'),
        ]);
        $pagesManuel[] = PageFactory::createOne([
            'title'  => 'Nos valeurs et notre mission',
            'slug'   => 'nos-valeurs-et-notre-mission',
            'content'     => '<p>' . $faker->realText(300) . '</p><p>' . $faker->realText(300) . '</p><p>' . $faker->realText(200) . '</p>',
            'status' => 'published',
            'publishedAt' => new \DateTimeImmutable('-2 months'),
        ]);
        // les pages doivent être créées PAR le ROLE_SUPER_ADMIN
        // tant qu'on a des pages
        foreach ($pagesManuel as $page) {
            $page->addUser($usersManuel[0]);
        }


        // PageFactory::createMany(3);

        // ── Formations ──────────────────────────────────────────────────
        $formations = [];
        // AD
        $formations[] = FormationFactory::createOne([
            'title'  => 'Aventure digitale',
            'slug'   => 'aventure-digitale',
            'description' => $faker->realText(1200),
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years', '-1 year')
            ),
            'status' => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-11 months', '-3 months')
            ),
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#00a199',
            'colorSecondary' => '#00589a',
        ]);
        // AM
        $formations[] = FormationFactory::createOne([
            'title'  => 'Animateur multimédia',
            'slug'   => 'animateur-multimedia',
            'description' => $faker->realText(1200),
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years', '-1 year')
            ),
            'status' => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-11 months', '-3 months')
            ),
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#532482',
            'colorSecondary' => '#00589a',
        ]);
        // TR
        $formations[] = FormationFactory::createOne([
            'title'  => 'Technicien PC & réseaux',
            'slug'   => 'technicien-reseaux',
            'description' => $faker->realText(1200),
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years', '-1 year')
            ),
            'status' => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-11 months', '-3 months')
            ),
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#e62c25',
            'colorSecondary' => '#00589a',
        ]);
        // DD
        $formations[] = FormationFactory::createOne([
            'title'  => 'Digital Designer',
            'slug'   => 'digital-designer',
            'description' => $faker->realText(1200),
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years', '-1 year')
            ),
            'status' => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-11 months', '-3 months')
            ),
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#05b7e9',
            'colorSecondary' => '#00589a',
        ]);
        // Web Dev
        $formations[] = FormationFactory::createOne([
            'title'  => 'Web Developer Full Stack',
            'slug'   => 'developpeur-web',
            'description' => $faker->realText(1200),
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years', '-1 year')
            ),
            'status' => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-11 months', '-3 months')
            ),
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#418a9e',
            'colorSecondary' => '#00589a',
        ]);
        // Chèques TIC
        $formations[] = FormationFactory::createOne([
            'title'  => 'Chèques TIC',
            'slug'   => 'cheques-tic',
            'description' => $faker->realText(1200),
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years', '-1 year')
            ),
            'status' => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-11 months', '-3 months')
            ),
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#0033a1',
            'colorSecondary' => '#00589a',
        ]);

        FormationFactory::createMany(2, fn () => [
            'status'    => 'draft',
            'createdBy' => $adminsAndFormateurs[array_rand($adminsAndFormateurs)],
            'colorPrimary' => '#FF5733',
            'colorSecondary' => '#00589a',
        ]);

        // Associer des responsables (formateurs) à chaque formation
        foreach ($formations as $formation) {
            $nbResponsables = random_int(1, 3);
            $responsables = array_slice($formateurs, 0, $nbResponsables);
            foreach ($responsables as $responsable) {
                $formation->addResponsable($responsable);
            }
        }
        $manager->flush();

        // ── Travaux (Works) ─────────────────────────────────────────────
        $works = [];
        foreach ($formations as $formation) {
            $nbWorks = random_int(1, 3);
            $newWorks = WorksFactory::createMany($nbWorks, fn () => [
                'createdAt'   => \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-3 years', '-1 year')
                ),
                'status' => 'published',
                'publishedAt' => new \DateTimeImmutable('-2 months'),
                'formation' => $formation,
            ]);
            $works = [...$works, ...$newWorks];

            // Associer des étudiants comme auteurs du travail
            foreach ($newWorks as $work) {
                $nbAuteurs = random_int(1, 3);
                $auteurs = array_slice($etudiants, 0, $nbAuteurs);
                foreach ($auteurs as $auteur) {
                    $work->addUser($auteur);
                }
            }
        }
        $manager->flush();

        // ── Commentaires ────────────────────────────────────────────────
        foreach (array_slice($works, 0, 20) as $work) {
            $nbComments = random_int(1, 4);
            CommentFactory::createMany($nbComments, fn () => [
                'works'    => $work,
                'user'     => $tousLesUsers[array_rand($tousLesUsers)],
                'approved' => (bool) random_int(0, 1),
            ]);
        }

        // ── Notations (Ratings) ─────────────────────────────────────────
        foreach (array_slice($works, 0, 15) as $work) {
            $nbRatings = random_int(1, 5);
            for ($i = 0; $i < $nbRatings; ++$i) {
                $rating = RatingFactory::createOne([
                    'user'  => $etudiants[array_rand($etudiants)],
                    'value' => random_int(1, 5),
                ]);
                $rating->addWork($work);
            }
        }
        $manager->flush();

        // ── Inscriptions ────────────────────────────────────────────────
        foreach (array_slice($formations, 0, 6) as $formation) {
            $nbInscriptions = random_int(3, 8);
            InscriptionFactory::createMany((int) ($nbInscriptions * 0.6), fn () => [
                'formation' => $formation,
                'treat'     => true,
                'treatAt'   => new \DateTimeImmutable(sprintf('-%d days', random_int(1, 90))),
            ]);
            InscriptionFactory::createMany((int) ceil($nbInscriptions * 0.4), fn () => [
                'formation' => $formation,
                'treat'     => false,
                'treatAt'   => null,
            ]);
        }

        // ── Messages de contact ─────────────────────────────────────────
        ContactMessageFactory::createMany(8);
        ContactMessageFactory::createMany(4, fn () => ['read' => true]);

        $manager->flush();
    }
}
