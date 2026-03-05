<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260304114038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'TINYINT UNSIGNED sur les booléens et DEFAULT CURRENT_TIMESTAMP sur toutes les colonnes created_at.';
    }

    public function up(Schema $schema): void
    {
        // Booléens → TINYINT UNSIGNED
        $this->addSql('ALTER TABLE comment MODIFY is_approved TINYINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE contact_message MODIFY is_read TINYINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE inscription MODIFY treat TINYINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE partenaire MODIFY is_active TINYINT UNSIGNED DEFAULT 0 NOT NULL');

        // created_at → DEFAULT CURRENT_TIMESTAMP
        $this->addSql('ALTER TABLE comment MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE contact_message MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE formation MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE inscription MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE page MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE rating MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE `user` MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE works MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // Retour booléens → TINYINT signé
        $this->addSql('ALTER TABLE comment MODIFY is_approved TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE contact_message MODIFY is_read TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE inscription MODIFY treat TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE partenaire MODIFY is_active TINYINT DEFAULT 0 NOT NULL');

        // Retour created_at sans DEFAULT
        $this->addSql('ALTER TABLE comment MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE contact_message MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE formation MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE inscription MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE page MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE rating MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `user` MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE works MODIFY created_at DATETIME NOT NULL');
    }
}
