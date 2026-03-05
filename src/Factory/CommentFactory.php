<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Comment;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Comment>
 */
final class CommentFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Comment::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'content'  => self::faker()->paragraph(),
            'approved' => false,
            'user'     => UserFactory::new(),
            'works'    => WorksFactory::new(),
        ];
    }

    /**
     * État : commentaire approuvé
     */
    public function approuve(): static
    {
        return $this->with(['approved' => true]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
