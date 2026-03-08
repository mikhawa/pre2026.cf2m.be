<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Works;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Works>
 */
class WorksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Works::class);
    }

    /**
     * Retourne les works publiés d'une formation, triés par date de publication DESC.
     *
     * @return Works[]
     */
    public function findPublishedByFormation(int $formationId): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.formation = :formation')
            ->andWhere('w.status = :status')
            ->setParameter('formation', $formationId)
            ->setParameter('status', 'published')
            ->orderBy('w.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
