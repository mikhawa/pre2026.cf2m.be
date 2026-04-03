<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ age (SMALLINT UNSIGNED) à la table inscription';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription ADD age SMALLINT UNSIGNED NOT NULL DEFAULT 18 AFTER message');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription DROP COLUMN age');
    }
}
