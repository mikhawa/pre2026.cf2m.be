<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email'                    => self::faker()->unique()->safeEmail(),
            'userName'                 => self::faker()->unique()->regexify('[a-zA-Z][a-zA-Z0-9_]{5,14}'),
            'roles'                    => [],
            'status'                   => 1,
            'biography'                => self::faker()->optional(0.7)->paragraph(),
            'externalLink1'            => self::faker()->optional(0.3)->url(),
            'externalLink2'            => null,
            'externalLink3'            => null,
            'activationToken'          => null,
            'resetPasswordToken'       => null,
            'resetPasswordRequestedAt' => null,
            'avatarName'               => null,
            'updatedAt'                => null,
        ];
    }

    /**
     * État : administrateur
     */
    public function admin(): static
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    /**
     * État : formateur
     */
    public function formateur(): static
    {
        return $this->with(['roles' => ['ROLE_FORMATEUR']]);
    }

    /**
     * État : compte banni
     */
    public function banni(): static
    {
        return $this->with(['status' => 2]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this->afterInstantiate(function (User $user): void {
            $plainPassword = $user->getPlainPassword() ?? 'password';
            $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
            $user->eraseCredentials();
        });
    }
}
