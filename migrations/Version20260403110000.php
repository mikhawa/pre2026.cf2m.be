<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ telephone (VARCHAR 30) à la table inscription';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE inscription ADD telephone VARCHAR(30) NOT NULL DEFAULT '' AFTER email");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription DROP COLUMN telephone');
    }
}
