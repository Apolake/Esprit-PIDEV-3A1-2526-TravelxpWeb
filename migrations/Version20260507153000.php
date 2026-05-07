<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add booking currency and pricing snapshot support.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE booking ADD currency VARCHAR(10) DEFAULT 'USD' NOT NULL, ADD pricing_snapshot JSON DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking DROP COLUMN currency, DROP COLUMN pricing_snapshot');
    }
}
