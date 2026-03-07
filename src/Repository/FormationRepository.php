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

    /** @return Formation[] */
    public function findAllPublished(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('f.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
