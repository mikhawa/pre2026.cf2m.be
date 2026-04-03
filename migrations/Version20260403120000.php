<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Correction types telephone (suppression DEFAULT) et age (SMALLINT → INT UNSIGNED)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription CHANGE telephone telephone VARCHAR(30) NOT NULL, CHANGE age age INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE inscription CHANGE telephone telephone VARCHAR(30) NOT NULL DEFAULT '', CHANGE age age SMALLINT UNSIGNED NOT NULL");
    }
}
