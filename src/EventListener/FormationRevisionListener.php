<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Formation;
use App\Entity\User;
use App\Service\RevisionService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Crée automatiquement une révision initiale (snapshot de création) à chaque
 * fois qu'une Formation est persistée pour la première fois.
 * Couvre toutes les sources : fixtures, EasyAdmin, import, etc.
 *
 * Stratégie : collecte les nouvelles formations dans postPersist,
 * puis crée les révisions dans postFlush (après que l'INSERT soit commis)
 * pour éviter les problèmes de UoW partiellement flushé avec flush_once.
 */
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postFlush)]
class FormationRevisionListener
{
    /** @var list<array{entity: Formation, author: User}> */
    private array $pendingRevisions = [];

    public function __construct(
        private readonly RevisionService $revisionService,
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Formation) {
            return;
        }

        $author = $entity->getCreatedBy();
        if (!$author instanceof User) {
            // Pas d'auteur assigné, impossible de tracer la création
            return;
        }

        // On mémorise la formation pour traitement dans postFlush
        $this->pendingRevisions[] = ['entity' => $entity, 'author' => $author];
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->pendingRevisions === []) {
            return;
        }

        // Vider la liste AVANT de créer les révisions pour éviter toute boucle infinie
        $pending = $this->pendingRevisions;
        $this->pendingRevisions = [];

        $em = $args->getObjectManager();

        foreach ($pending as $item) {
            // isCreation = true : previousData restera null ("Création initiale")
            // autoApprove = true : la création est toujours considérée comme approuvée
            $this->revisionService->createRevision($item['entity'], $item['author'], true, isCreation: true);
        }

        // Second flush pour persister les révisions créées
        $em->flush();
    }
}
