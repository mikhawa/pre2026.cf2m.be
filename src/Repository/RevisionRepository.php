<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Revision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Revision>
 */
class RevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Revision::class);
    }

    /**
     * Compte le nombre de révisions en attente de validation.
     */
    public function findPendingCount(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.status = :status')
            ->setParameter('status', Revision::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les révisions filtrées par type d'entité.
     *
     * @return Revision[]
     */
    public function findByEntityType(string $type): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.entityType = :type')
            ->setParameter('type', $type)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne l'historique complet d'une Formation, trié par date descendante.
     *
     * @return Revision[]
     */
    public function findByFormationId(int $formationId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.entityType = :type')
            ->andWhere('r.entityId = :id')
            ->setParameter('type', 'formation')
            ->setParameter('id', $formationId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
