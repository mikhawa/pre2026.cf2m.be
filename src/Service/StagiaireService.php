<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Formation;
use App\Entity\FormationStagiaire;
use App\Entity\User;
use App\Repository\FormationStagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gère l'appartenance des stagiaires à une formation (entité pivot FormationStagiaire)
 * et la synchronisation physique du rôle global ROLE_STAGIAIRE (stratégie A).
 *
 * Règle de synchronisation : un utilisateur possède ROLE_STAGIAIRE en base si et
 * seulement s'il est rattaché à au moins une formation via FormationStagiaire.
 */
class StagiaireService
{
    public const ROLE_STAGIAIRE = 'ROLE_STAGIAIRE';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FormationStagiaireRepository $formationStagiaireRepository,
    ) {
    }

    /**
     * Ajoute un utilisateur comme stagiaire d'une formation et synchronise ROLE_STAGIAIRE.
     *
     * Idempotent : si l'utilisateur est déjà stagiaire de cette formation, la ligne
     * existante est retournée sans créer de doublon (choix retenu pour simplifier le
     * traitement côté contrôleur ; la contrainte d'unicité (formation, user) reste le
     * garde-fou en base).
     */
    public function ajouterStagiaire(Formation $formation, User $user, User $addedBy): FormationStagiaire
    {
        $existant = $this->formationStagiaireRepository->findOneForFormationAndUser($formation, $user);
        if (null !== $existant) {
            return $existant;
        }

        $stagiaire = new FormationStagiaire();
        $stagiaire->setFormation($formation);
        $stagiaire->setUser($user);
        $stagiaire->setAddedBy($addedBy);

        $this->entityManager->persist($stagiaire);

        // Synchronisation physique : l'utilisateur a désormais au moins une formation.
        $user->addRole(self::ROLE_STAGIAIRE);

        $this->entityManager->flush();

        return $stagiaire;
    }

    /**
     * Retire un utilisateur d'une formation et synchronise ROLE_STAGIAIRE.
     *
     * @return bool true s'il s'agissait de sa DERNIÈRE formation (l'utilisateur perd
     *              alors ROLE_STAGIAIRE), false sinon (ou si l'utilisateur n'était pas
     *              stagiaire de cette formation)
     */
    public function retirerStagiaire(Formation $formation, User $user): bool
    {
        $stagiaire = $this->formationStagiaireRepository->findOneForFormationAndUser($formation, $user);
        if (null === $stagiaire) {
            return false;
        }

        $this->entityManager->remove($stagiaire);
        $this->entityManager->flush();

        // Recompte après suppression : si plus aucune formation, on retire le rôle global.
        $derniere = 0 === $this->formationStagiaireRepository->countForUser($user);
        if ($derniere) {
            $user->removeRole(self::ROLE_STAGIAIRE);
            $this->entityManager->flush();
        }

        return $derniere;
    }
}
