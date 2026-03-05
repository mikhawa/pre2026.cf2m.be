<?php

namespace App\Story;

use App\Factory\FormationFactory;
use App\Factory\InscriptionFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        // Super administrateur Mikhawa
        UserFactory::createOne([
            'email'         => 'mikhawa@cf2m.be',
            'userName'      => 'Mikhawa',
            'roles'         => ['ROLE_SUPER_ADMIN'],
            'status'        => 1,
            'plainPassword' => '123mikhawa',
        ]);

        // Utilisateurs de test
        UserFactory::createMany(2, ['roles' => ['ROLE_ADMIN']]);
        UserFactory::createMany(5, ['roles' => ['ROLE_FORMATEUR']]);
        UserFactory::createMany(20);

        // Formations
        FormationFactory::new()->publiee()->createMany(8);
        FormationFactory::new()->brouillon()->createMany(4);

        // Inscriptions de test
        InscriptionFactory::createMany(30);
        InscriptionFactory::new()->traitee()->createMany(10);
    }
}
