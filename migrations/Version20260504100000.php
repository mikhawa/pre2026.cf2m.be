<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout approved_by_id et approved_at sur comment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment ADD approved_by_id INT UNSIGNED DEFAULT NULL, ADD approved_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C77B5BEAC FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9474526C77B5BEAC ON comment (approved_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C77B5BEAC');
        $this->addSql('DROP INDEX IDX_9474526C77B5BEAC ON comment');
        $this->addSql('ALTER TABLE comment DROP approved_by_id, DROP approved_at');
    }
}
