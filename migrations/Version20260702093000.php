<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crée la table pivot formation_stagiaire (gestion des stagiaires par formation).
 *
 * Aucune donnée n'est insérée : les ROLE_STAGIAIRE existants conservent leur rôle
 * en base sans formation associée, jusqu'à rattachement manuel par un gestionnaire.
 */
final class Version20260702093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table pivot formation_stagiaire (stagiaires par formation)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE formation_stagiaire (id INT UNSIGNED AUTO_INCREMENT NOT NULL, formation_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, added_by_id INT UNSIGNED DEFAULT NULL, added_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FS_FORMATION (formation_id), INDEX IDX_FS_USER (user_id), INDEX IDX_FS_ADDED_BY (added_by_id), UNIQUE INDEX uq_formation_stagiaire (formation_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation_stagiaire ADD CONSTRAINT FK_FS_FORMATION FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_stagiaire ADD CONSTRAINT FK_FS_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_stagiaire ADD CONSTRAINT FK_FS_ADDED_BY FOREIGN KEY (added_by_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation_stagiaire DROP FOREIGN KEY FK_FS_FORMATION');
        $this->addSql('ALTER TABLE formation_stagiaire DROP FOREIGN KEY FK_FS_USER');
        $this->addSql('ALTER TABLE formation_stagiaire DROP FOREIGN KEY FK_FS_ADDED_BY');
        $this->addSql('DROP TABLE formation_stagiaire');
    }
}
