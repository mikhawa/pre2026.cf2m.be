<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Formation;
use App\Entity\FormationHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormationHistory>
 */
class FormationHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationHistory::class);
    }

    /**
     * Retourne le prochain numéro de version pour une formation donnée.
     * La contrainte UNIQUE (formation_id, version) sert de garde-fou.
     */
    public function getNextVersion(Formation $formation): int
    {
        $result = $this->createQueryBuilder('h')
            ->select('MAX(h.version)')
            ->where('h.formation = :formation')
            ->setParameter('formation', $formation)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $result) + 1;
    }

    /**
     * Retourne l'historique complet d'une formation, du plus récent au plus ancien.
     *
     * @return FormationHistory[]
     */
    public function findHistoryForFormation(Formation $formation): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.formation = :formation')
            ->setParameter('formation', $formation)
            ->orderBy('h.version', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la version actuelle (auto-approuvée la plus récente) d'une formation.
     */
    public function findCurrentVersion(Formation $formation): ?FormationHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.formation = :formation')
            ->andWhere('h.revisionStatus = :status')
            ->setParameter('formation', $formation)
            ->setParameter('status', FormationHistory::STATUS_AUTO_APPROVED)
            ->orderBy('h.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retourne la révision en attente d'une formation, s'il en existe une.
     */
    public function findPendingForFormation(Formation $formation): ?FormationHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.formation = :formation')
            ->andWhere('h.revisionStatus = :status')
            ->setParameter('formation', $formation)
            ->setParameter('status', FormationHistory::STATUS_PENDING)
            ->orderBy('h.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte les révisions en attente (toutes formations confondues).
     */
    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.revisionStatus = :status')
            ->setParameter('status', FormationHistory::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
