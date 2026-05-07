<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507162000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing service description and availability columns.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service ADD description LONGTEXT DEFAULT NULL, ADD is_available TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service DROP description, DROP is_available');
    }
}
