<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Formation;
use App\Entity\FormationStagiaire;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormationStagiaire>
 */
class FormationStagiaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationStagiaire::class);
    }

    /**
     * Compte le nombre de formations auxquelles un utilisateur est rattaché comme stagiaire.
     *
     * Utilisé par la synchronisation de ROLE_STAGIAIRE : on compte via une requête
     * (et non via une collection en mémoire) pour disposer d'une valeur fiable même
     * après une suppression non encore reflétée dans une collection chargée.
     */
    public function countForUser(User $user): int
    {
        return (int) $this->createQueryBuilder('fs')
            ->select('COUNT(fs.id)')
            ->where('fs.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Indique si un utilisateur est déjà stagiaire d'une formation donnée.
     */
    public function existsForFormationAndUser(Formation $formation, User $user): bool
    {
        $count = (int) $this->createQueryBuilder('fs')
            ->select('COUNT(fs.id)')
            ->where('fs.formation = :formation')
            ->andWhere('fs.user = :user')
            ->setParameter('formation', $formation)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Retrouve la ligne pivot pour un couple (formation, utilisateur), si elle existe.
     */
    public function findOneForFormationAndUser(Formation $formation, User $user): ?FormationStagiaire
    {
        return $this->findOneBy(['formation' => $formation, 'user' => $user]);
    }

    /**
     * Liste les stagiaires d'une formation, triés par date d'ajout (du plus ancien au plus récent).
     *
     * @return FormationStagiaire[]
     */
    public function findForFormation(Formation $formation): array
    {
        return $this->createQueryBuilder('fs')
            ->where('fs.formation = :formation')
            ->setParameter('formation', $formation)
            ->orderBy('fs.addedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
