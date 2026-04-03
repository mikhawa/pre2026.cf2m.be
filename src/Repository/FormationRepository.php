<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function findOneBySlug(string $slug): ?Formation
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Retourne les formations visibles sur le site (publiées + en recrutement).
     * Les formations en recrutement apparaissent en premier.
     *
     * @return Formation[]
     */
    public function findAllPublished(): array
    {
        return $this->createQueryBuilder('f')
            ->addSelect('CASE WHEN f.status = :recruiting THEN 0 ELSE 1 END AS HIDDEN statusOrder')
            ->where('f.status IN (:statuses)')
            ->setParameter('statuses', ['published', 'recruiting'])
            ->setParameter('recruiting', 'recruiting')
            ->orderBy('statusOrder', 'ASC')
            ->addOrderBy('f.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
