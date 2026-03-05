<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304112351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création initiale du schéma : user, formation, works, comment, rating, inscription, contact_message, page, partenaire et tables de jointure.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, is_approved TINYINT UNSIGNED DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, user_id INT UNSIGNED NOT NULL, works_id INT UNSIGNED NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526CF6CB822A (works_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE contact_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, sujet VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, is_read TINYINT UNSIGNED DEFAULT 0 NOT NULL, read_by_id INT UNSIGNED DEFAULT NULL, INDEX IDX_2C9211FEF5675CD0 (read_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE formation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_by_id INT UNSIGNED NOT NULL, updated_by_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_404021BF989D9B62 (slug), INDEX IDX_404021BFB03A8386 (created_by_id), INDEX IDX_404021BF896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE formation_user (formation_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_DA4C33095200282E (formation_id), INDEX IDX_DA4C3309A76ED395 (user_id), PRIMARY KEY (formation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE inscription (id INT UNSIGNED AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, treat TINYINT UNSIGNED DEFAULT 0 NOT NULL, treat_at DATETIME DEFAULT NULL, formation_id INT UNSIGNED NOT NULL, treat_by_id INT UNSIGNED DEFAULT NULL, INDEX IDX_5E90F6D65200282E (formation_id), INDEX IDX_5E90F6D6544F38F5 (treat_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE page (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, published_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE page_user (page_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_A57CA93C4663E4 (page_id), INDEX IDX_A57CA93A76ED395 (user_id), PRIMARY KEY (page_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE partenaire (id INT UNSIGNED AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, is_active TINYINT UNSIGNED DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE rating (id INT UNSIGNED AUTO_INCREMENT NOT NULL, value SMALLINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, user_id INT UNSIGNED NOT NULL, INDEX IDX_D8892622A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE comment_rating (rating_id INT UNSIGNED NOT NULL, comment_id INT UNSIGNED NOT NULL, INDEX IDX_129A7E30A32EFC6 (rating_id), INDEX IDX_129A7E30F8697D13 (comment_id), PRIMARY KEY (rating_id, comment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE rating_works (rating_id INT UNSIGNED NOT NULL, works_id INT UNSIGNED NOT NULL, INDEX IDX_6BF30D7FA32EFC6 (rating_id), INDEX IDX_6BF30D7FF6CB822A (works_id), PRIMARY KEY (rating_id, works_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `user` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, user_name VARCHAR(50) NOT NULL, activation_token VARCHAR(64) DEFAULT NULL, status SMALLINT UNSIGNED DEFAULT 0 NOT NULL, reset_password_token VARCHAR(64) DEFAULT NULL, reset_password_requested_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, avatar_name VARCHAR(255) DEFAULT NULL, biography VARCHAR(600) DEFAULT NULL, external_link1 VARCHAR(255) DEFAULT NULL, external_link2 VARCHAR(255) DEFAULT NULL, external_link3 VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64924A232CF (user_name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE works (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, published_at DATETIME DEFAULT NULL, formation_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_F6E50243989D9B62 (slug), INDEX IDX_F6E502435200282E (formation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE works_user (works_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_88231830F6CB822A (works_id), INDEX IDX_88231830A76ED395 (user_id), PRIMARY KEY (works_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF6CB822A FOREIGN KEY (works_id) REFERENCES works (id)');
        $this->addSql('ALTER TABLE contact_message ADD CONSTRAINT FK_2C9211FEF5675CD0 FOREIGN KEY (read_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation_user ADD CONSTRAINT FK_DA4C33095200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_user ADD CONSTRAINT FK_DA4C3309A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D65200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6544F38F5 FOREIGN KEY (treat_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE page_user ADD CONSTRAINT FK_A57CA93C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_user ADD CONSTRAINT FK_A57CA93A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment_rating ADD CONSTRAINT FK_129A7E30A32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_rating ADD CONSTRAINT FK_129A7E30F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating_works ADD CONSTRAINT FK_6BF30D7FA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating_works ADD CONSTRAINT FK_6BF30D7FF6CB822A FOREIGN KEY (works_id) REFERENCES works (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE works ADD CONSTRAINT FK_F6E502435200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE works_user ADD CONSTRAINT FK_88231830F6CB822A FOREIGN KEY (works_id) REFERENCES works (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE works_user ADD CONSTRAINT FK_88231830A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF6CB822A');
        $this->addSql('ALTER TABLE contact_message DROP FOREIGN KEY FK_2C9211FEF5675CD0');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFB03A8386');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF896DBBDE');
        $this->addSql('ALTER TABLE formation_user DROP FOREIGN KEY FK_DA4C33095200282E');
        $this->addSql('ALTER TABLE formation_user DROP FOREIGN KEY FK_DA4C3309A76ED395');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D65200282E');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6544F38F5');
        $this->addSql('ALTER TABLE page_user DROP FOREIGN KEY FK_A57CA93C4663E4');
        $this->addSql('ALTER TABLE page_user DROP FOREIGN KEY FK_A57CA93A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('ALTER TABLE comment_rating DROP FOREIGN KEY FK_129A7E30A32EFC6');
        $this->addSql('ALTER TABLE comment_rating DROP FOREIGN KEY FK_129A7E30F8697D13');
        $this->addSql('ALTER TABLE rating_works DROP FOREIGN KEY FK_6BF30D7FA32EFC6');
        $this->addSql('ALTER TABLE rating_works DROP FOREIGN KEY FK_6BF30D7FF6CB822A');
        $this->addSql('ALTER TABLE works DROP FOREIGN KEY FK_F6E502435200282E');
        $this->addSql('ALTER TABLE works_user DROP FOREIGN KEY FK_88231830F6CB822A');
        $this->addSql('ALTER TABLE works_user DROP FOREIGN KEY FK_88231830A76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_user');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE page_user');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE comment_rating');
        $this->addSql('DROP TABLE rating_works');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE works');
        $this->addSql('DROP TABLE works_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
