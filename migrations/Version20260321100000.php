<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout de description_courte (VARCHAR 800) et logo (VARCHAR 255)
 * sur les tables formation et formation_history.
 */
final class Version20260321100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout description_courte et logo sur formation et formation_history';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation ADD description_courte VARCHAR(800) DEFAULT NULL, ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE formation_history ADD description_courte VARCHAR(800) DEFAULT NULL, ADD logo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation DROP COLUMN description_courte, DROP COLUMN logo');
        $this->addSql('ALTER TABLE formation_history DROP COLUMN description_courte, DROP COLUMN logo');
    }
}
