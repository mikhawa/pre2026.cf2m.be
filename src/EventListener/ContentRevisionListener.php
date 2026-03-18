<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Formation;
use App\Entity\Page;
use App\Entity\User;
use App\Entity\Works;
use App\Service\RevisionService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Crée automatiquement une révision initiale (snapshot de création) à chaque
 * fois qu'une Formation, Page ou Works est persistée pour la première fois.
 *
 * Stratégie : collecte les nouvelles entités dans postPersist,
 * puis crée les révisions dans postFlush (après que l'INSERT soit commis).
 *
 * Note : fonctionne nativement pour :
 *   - Formation (createdBy défini à la création)
 *   - Page/Works créées via EasyAdmin (associations ManyToMany définies avant flush)
 *
 * Pour les fixtures (flush_once: true), les associations ManyToMany de Page/Works
 * sont insérées dans un flush distinct. Les révisions initiales pour ces entités
 * sont créées explicitement dans AppFixtures.
 *
 * Pour Page/Works, l'auteur est résolu via DBAL sur la table de jonction
 * pour contourner le lazy-loading Doctrine avec les proxies Foundry.
 */
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postFlush)]
class ContentRevisionListener
{
    /** @var list<Formation|Page|Works> */
    private array $pendingEntities = [];

    public function __construct(
        private readonly RevisionService $revisionService,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Formation && !$entity instanceof Page && !$entity instanceof Works) {
            return;
        }

        // On mémorise l'entité pour traitement dans postFlush.
        // Pour les ManyToMany (Page/Works), la résolution de l'auteur est différée
        // car les tables de jonction peuvent ne pas être encore insérées à ce stade.
        $this->pendingEntities[] = $entity;
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->pendingEntities === []) {
            return;
        }

        // Vider la liste AVANT de créer les révisions pour éviter toute boucle infinie
        $pending = $this->pendingEntities;
        $this->pendingEntities = [];

        $em = $args->getObjectManager();
        $hasRevision = false;

        foreach ($pending as $entity) {
            $author = $this->resolveAuthor($entity);
            if (!$author instanceof User) {
                // Pas d'auteur assigné (ex. fixtures avec flush_once avant addUser)
                continue;
            }

            // isCreation = true : previousData restera null ("Création initiale")
            // autoApprove = true : la création est toujours considérée comme approuvée
            $this->revisionService->createRevision($entity, $author, true, isCreation: true);
            $hasRevision = true;
        }

        if ($hasRevision) {
            // Second flush pour persister les révisions créées
            $em->flush();
        }
    }

    /**
     * Résout l'auteur selon le type d'entité :
     * - Formation : champ createdBy (ManyToOne, sur la ligne)
     * - Page / Works : premier user via DBAL sur la table de jonction
     */
    private function resolveAuthor(Formation|Page|Works $entity): ?User
    {
        if ($entity instanceof Formation) {
            $author = $entity->getCreatedBy();

            return $author instanceof User ? $author : null;
        }

        // Page et Works : requête DBAL directe sur la table de jonction.
        // Contourne le lazy-loading qui échoue avec les proxies Foundry
        // (collection vide si auto-refresh en REPEATABLE READ ou flush_once).
        $id = method_exists($entity, 'getId') ? $entity->getId() : null;
        if (!$id) {
            return null;
        }

        [$table, $idColumn] = $entity instanceof Page
            ? ['page_user', 'page_id']
            : ['works_user', 'works_id'];

        $userId = $this->em->getConnection()->fetchOne(
            sprintf('SELECT user_id FROM `%s` WHERE `%s` = ? LIMIT 1', $table, $idColumn),
            [$id]
        );

        if (!$userId) {
            return null;
        }

        return $this->em->find(User::class, (int) $userId);
    }
}
