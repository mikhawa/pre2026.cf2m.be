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

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ── Super administrateur ────────────────────────────────────────
        UserFactory::createOne([
            'email'         => 'mikhawa@cf2m.be',
            'userName'      => 'Mikhawa',
            'roles'         => ['ROLE_SUPER_ADMIN'],
            'status'        => 1,
            'plainPassword' => '123mikhawa',
        ]);

        // ── Utilisateurs ────────────────────────────────────────────────
        $admins     = UserFactory::createMany(2, ['roles' => ['ROLE_ADMIN']]);
        $formateurs = UserFactory::createMany(5, fn () => ['roles' => ['ROLE_FORMATEUR']]);
        $etudiants  = UserFactory::createMany(25);

        $tousLesUsers = [...$admins, ...$formateurs, ...$etudiants];

        // ── Partenaires ─────────────────────────────────────────────────
        PartenaireFactory::createMany(6);
        PartenaireFactory::createMany(2, fn () => ['active' => false]);

        // ── Pages CMS ───────────────────────────────────────────────────
        PageFactory::createOne([
            'title'       => 'Accueil',
            'slug'        => 'accueil',
            'status'      => 'published',
            'publishedAt' => new \DateTimeImmutable('-1 month'),
        ]);
        PageFactory::createOne([
            'title'       => 'À propos',
            'slug'        => 'a-propos',
            'status'      => 'published',
            'publishedAt' => new \DateTimeImmutable('-2 months'),
        ]);
        PageFactory::createOne([
            'title'  => 'Contact',
            'slug'   => 'contact',
            'status' => 'draft',
        ]);
        PageFactory::createMany(3);

        // ── Formations ──────────────────────────────────────────────────
        $formations = FormationFactory::createMany(8, fn () => [
            'status'    => 'published',
            'createdBy' => $formateurs[array_rand($formateurs)],
        ]);

        FormationFactory::createMany(2, fn () => [
            'status'    => 'draft',
            'createdBy' => $formateurs[array_rand($formateurs)],
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
            $nbWorks = random_int(2, 5);
            $newWorks = WorksFactory::createMany($nbWorks, fn () => [
                'status'    => 'published',
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
