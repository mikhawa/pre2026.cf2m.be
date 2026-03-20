<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use App\Entity\PageHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageHistory>
 */
class PageHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageHistory::class);
    }

    public function getNextVersion(Page $page): int
    {
        $result = $this->createQueryBuilder('h')
            ->select('MAX(h.version)')
            ->where('h.page = :page')
            ->setParameter('page', $page)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $result) + 1;
    }

    /** @return PageHistory[] */
    public function findHistoryForPage(Page $page): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.page = :page')
            ->setParameter('page', $page)
            ->orderBy('h.version', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCurrentVersion(Page $page): ?PageHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.page = :page')
            ->andWhere('h.revisionStatus = :status')
            ->setParameter('page', $page)
            ->setParameter('status', PageHistory::STATUS_AUTO_APPROVED)
            ->orderBy('h.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPendingForPage(Page $page): ?PageHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.page = :page')
            ->andWhere('h.revisionStatus = :status')
            ->setParameter('page', $page)
            ->setParameter('status', PageHistory::STATUS_PENDING)
            ->orderBy('h.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.revisionStatus = :status')
            ->setParameter('status', PageHistory::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
