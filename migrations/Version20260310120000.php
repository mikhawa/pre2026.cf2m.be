<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout des colonnes color_primary et color_secondary sur la table formation.
 */
final class Version20260310120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs de couleur (primaire et secondaire) à la table formation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation ADD color_primary VARCHAR(7) DEFAULT NULL, ADD color_secondary VARCHAR(7) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation DROP color_primary, DROP color_secondary');
    }
}
