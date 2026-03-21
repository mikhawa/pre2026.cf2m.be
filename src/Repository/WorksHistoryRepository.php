<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Works;
use App\Entity\WorksHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorksHistory>
 */
class WorksHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorksHistory::class);
    }

    public function getNextVersion(Works $works): int
    {
        $result = $this->createQueryBuilder('h')
            ->select('MAX(h.version)')
            ->where('h.works = :works')
            ->setParameter('works', $works)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $result) + 1;
    }

    /** @return WorksHistory[] */
    public function findHistoryForWorks(Works $works): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.works = :works')
            ->setParameter('works', $works)
            ->orderBy('h.version', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCurrentVersion(Works $works): ?WorksHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.works = :works')
            ->andWhere('h.revisionStatus = :status')
            ->setParameter('works', $works)
            ->setParameter('status', WorksHistory::STATUS_AUTO_APPROVED)
            ->orderBy('h.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPendingForWorks(Works $works): ?WorksHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.works = :works')
            ->andWhere('h.revisionStatus = :status')
            ->setParameter('works', $works)
            ->setParameter('status', WorksHistory::STATUS_PENDING)
            ->orderBy('h.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retourne toutes les révisions en attente (tous works confondus), les plus récentes en premier.
     *
     * @return WorksHistory[]
     */
    public function findAllPending(): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.revisionStatus = :status')
            ->setParameter('status', WorksHistory::STATUS_PENDING)
            ->orderBy('h.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne une version précise de l'historique pour un works donné.
     */
    public function findByVersion(Works $works, int $version): ?WorksHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.works = :works')
            ->andWhere('h.version = :version')
            ->setParameter('works', $works)
            ->setParameter('version', $version)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.revisionStatus = :status')
            ->setParameter('status', WorksHistory::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
