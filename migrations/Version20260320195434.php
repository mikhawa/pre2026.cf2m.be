<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320195434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Phase 5 — Suppression de la table revision (remplacée par formation_history, page_history, works_history)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE revision DROP FOREIGN KEY `FK_6D6315CCB03A8386`');
        $this->addSql('ALTER TABLE revision DROP FOREIGN KEY `FK_6D6315CCFC6B21F1`');
        $this->addSql('DROP TABLE revision');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revision (id INT UNSIGNED AUTO_INCREMENT NOT NULL, entity_type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, entity_id INT UNSIGNED NOT NULL, entity_title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, data JSON NOT NULL, status SMALLINT UNSIGNED DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, review_note LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_by_id INT UNSIGNED NOT NULL, reviewed_by_id INT UNSIGNED DEFAULT NULL, previous_data JSON DEFAULT NULL, INDEX IDX_6D6315CCB03A8386 (created_by_id), INDEX IDX_6D6315CCFC6B21F1 (reviewed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE revision ADD CONSTRAINT `FK_6D6315CCB03A8386` FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE revision ADD CONSTRAINT `FK_6D6315CCFC6B21F1` FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
    }
}
