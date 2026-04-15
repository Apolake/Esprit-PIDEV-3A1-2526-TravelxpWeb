<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260411110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add latitude and longitude columns to properties for map display and routing.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE properties ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE properties DROP latitude, DROP longitude');
    }
}
