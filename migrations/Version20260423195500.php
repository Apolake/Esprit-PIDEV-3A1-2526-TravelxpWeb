<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260423195500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add booking currency, payment tracking, and pricing snapshot support for advanced booking features.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE bookings ADD currency VARCHAR(10) DEFAULT 'USD' NOT NULL, ADD payment_status VARCHAR(20) DEFAULT 'unpaid' NOT NULL, ADD payment_reference VARCHAR(80) DEFAULT NULL, ADD pricing_snapshot JSON DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bookings DROP COLUMN currency, DROP COLUMN payment_status, DROP COLUMN payment_reference, DROP COLUMN pricing_snapshot');
    }
}
