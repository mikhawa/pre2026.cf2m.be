<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320113106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, version SMALLINT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, color_primary VARCHAR(7) DEFAULT NULL, color_secondary VARCHAR(7) DEFAULT NULL, published_at DATETIME DEFAULT NULL, revision_status SMALLINT UNSIGNED DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, review_note LONGTEXT DEFAULT NULL, formation_id INT UNSIGNED NOT NULL, created_by_id INT UNSIGNED NOT NULL, reviewed_by_id INT UNSIGNED DEFAULT NULL, INDEX IDX_624535305200282E (formation_id), INDEX IDX_62453530B03A8386 (created_by_id), INDEX IDX_62453530FC6B21F1 (reviewed_by_id), INDEX idx_fh_revision_status (revision_status), INDEX idx_fh_created_at (created_at), UNIQUE INDEX uq_formation_version (formation_id, version), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE formation_history_responsable (formation_history_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_4BFD0B65E6AD576B (formation_history_id), INDEX IDX_4BFD0B65A76ED395 (user_id), PRIMARY KEY (formation_history_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE page_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, version SMALLINT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, published_at DATETIME DEFAULT NULL, revision_status SMALLINT UNSIGNED DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, review_note LONGTEXT DEFAULT NULL, page_id INT UNSIGNED NOT NULL, created_by_id INT UNSIGNED NOT NULL, reviewed_by_id INT UNSIGNED DEFAULT NULL, INDEX IDX_DDDA9BDEC4663E4 (page_id), INDEX IDX_DDDA9BDEB03A8386 (created_by_id), INDEX IDX_DDDA9BDEFC6B21F1 (reviewed_by_id), INDEX idx_ph_revision_status (revision_status), INDEX idx_ph_created_at (created_at), UNIQUE INDEX uq_page_version (page_id, version), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE page_history_user (page_history_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_38BA0A7B15E5B5CC (page_history_id), INDEX IDX_38BA0A7BA76ED395 (user_id), PRIMARY KEY (page_history_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE works_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, version SMALLINT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, published_at DATETIME DEFAULT NULL, revision_status SMALLINT UNSIGNED DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, review_note LONGTEXT DEFAULT NULL, works_id INT UNSIGNED NOT NULL, formation_id INT UNSIGNED NOT NULL, created_by_id INT UNSIGNED NOT NULL, reviewed_by_id INT UNSIGNED DEFAULT NULL, INDEX IDX_E2826ADDF6CB822A (works_id), INDEX IDX_E2826ADD5200282E (formation_id), INDEX IDX_E2826ADDB03A8386 (created_by_id), INDEX IDX_E2826ADDFC6B21F1 (reviewed_by_id), INDEX idx_wh_revision_status (revision_status), INDEX idx_wh_created_at (created_at), UNIQUE INDEX uq_works_version (works_id, version), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE works_history_user (works_history_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_7BB1545D7FB40290 (works_history_id), INDEX IDX_7BB1545DA76ED395 (user_id), PRIMARY KEY (works_history_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE formation_history ADD CONSTRAINT FK_624535305200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_history ADD CONSTRAINT FK_62453530B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation_history ADD CONSTRAINT FK_62453530FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation_history_responsable ADD CONSTRAINT FK_4BFD0B65E6AD576B FOREIGN KEY (formation_history_id) REFERENCES formation_history (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_history_responsable ADD CONSTRAINT FK_4BFD0B65A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_history ADD CONSTRAINT FK_DDDA9BDEC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_history ADD CONSTRAINT FK_DDDA9BDEB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE page_history ADD CONSTRAINT FK_DDDA9BDEFC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE page_history_user ADD CONSTRAINT FK_38BA0A7B15E5B5CC FOREIGN KEY (page_history_id) REFERENCES page_history (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_history_user ADD CONSTRAINT FK_38BA0A7BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE works_history ADD CONSTRAINT FK_E2826ADDF6CB822A FOREIGN KEY (works_id) REFERENCES works (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE works_history ADD CONSTRAINT FK_E2826ADD5200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE works_history ADD CONSTRAINT FK_E2826ADDB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE works_history ADD CONSTRAINT FK_E2826ADDFC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE works_history_user ADD CONSTRAINT FK_7BB1545D7FB40290 FOREIGN KEY (works_history_id) REFERENCES works_history (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE works_history_user ADD CONSTRAINT FK_7BB1545DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE revision CHANGE previous_data previous_data JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation_history DROP FOREIGN KEY FK_624535305200282E');
        $this->addSql('ALTER TABLE formation_history DROP FOREIGN KEY FK_62453530B03A8386');
        $this->addSql('ALTER TABLE formation_history DROP FOREIGN KEY FK_62453530FC6B21F1');
        $this->addSql('ALTER TABLE formation_history_responsable DROP FOREIGN KEY FK_4BFD0B65E6AD576B');
        $this->addSql('ALTER TABLE formation_history_responsable DROP FOREIGN KEY FK_4BFD0B65A76ED395');
        $this->addSql('ALTER TABLE page_history DROP FOREIGN KEY FK_DDDA9BDEC4663E4');
        $this->addSql('ALTER TABLE page_history DROP FOREIGN KEY FK_DDDA9BDEB03A8386');
        $this->addSql('ALTER TABLE page_history DROP FOREIGN KEY FK_DDDA9BDEFC6B21F1');
        $this->addSql('ALTER TABLE page_history_user DROP FOREIGN KEY FK_38BA0A7B15E5B5CC');
        $this->addSql('ALTER TABLE page_history_user DROP FOREIGN KEY FK_38BA0A7BA76ED395');
        $this->addSql('ALTER TABLE works_history DROP FOREIGN KEY FK_E2826ADDF6CB822A');
        $this->addSql('ALTER TABLE works_history DROP FOREIGN KEY FK_E2826ADD5200282E');
        $this->addSql('ALTER TABLE works_history DROP FOREIGN KEY FK_E2826ADDB03A8386');
        $this->addSql('ALTER TABLE works_history DROP FOREIGN KEY FK_E2826ADDFC6B21F1');
        $this->addSql('ALTER TABLE works_history_user DROP FOREIGN KEY FK_7BB1545D7FB40290');
        $this->addSql('ALTER TABLE works_history_user DROP FOREIGN KEY FK_7BB1545DA76ED395');
        $this->addSql('DROP TABLE formation_history');
        $this->addSql('DROP TABLE formation_history_responsable');
        $this->addSql('DROP TABLE page_history');
        $this->addSql('DROP TABLE page_history_user');
        $this->addSql('DROP TABLE works_history');
        $this->addSql('DROP TABLE works_history_user');
        $this->addSql('ALTER TABLE revision CHANGE previous_data previous_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }
}
