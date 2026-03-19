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
     * Compte le nombre de révisions pour une entité donnée (type + id).
     */
    public function countByEntityId(string $type, int $id): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.entityType = :type')
            ->andWhere('r.entityId = :id')
            ->setParameter('type', $type)
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne l'historique complet d'une Formation, trié par date descendante.
     *
     * @return Revision[]
     */
    public function findByFormationId(int $formationId): array
    {
        return $this->findByEntityId('formation', $formationId);
    }

    /**
     * Retourne la révision PENDING en cours pour une entité donnée, ou null si aucune.
     */
    public function findPendingForEntity(string $entityType, int $entityId): ?Revision
    {
        return $this->findOneBy([
            'entityType' => $entityType,
            'entityId'   => $entityId,
            'status'     => Revision::STATUS_PENDING,
        ]);
    }

    /**
     * Retourne l'historique complet d'une entité quelconque, trié par date descendante.
     *
     * @return Revision[]
     */
    public function findByEntityId(string $type, int $id): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.entityType = :type')
            ->andWhere('r.entityId = :id')
            ->setParameter('type', $type)
            ->setParameter('id', $id)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
