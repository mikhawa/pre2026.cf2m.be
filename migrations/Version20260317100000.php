<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout du champ previous_data (JSON nullable) sur la table revision.
 * Stocke le snapshot de l'état précédent directement dans la révision,
 * supprimant ainsi le besoin de créer une double entrée lors de l'approbation.
 */
final class Version20260317100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de previous_data (JSON nullable) sur revision pour éviter la double entrée lors de l\'approbation.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE revision ADD previous_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE revision DROP COLUMN previous_data');
    }
}
