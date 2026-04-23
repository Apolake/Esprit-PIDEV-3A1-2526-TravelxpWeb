<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260419093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trip destination coordinates and activity location coordinates for map/weather integration.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trips ADD destination_latitude DOUBLE PRECISION DEFAULT NULL, ADD destination_longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE activities ADD location_latitude DOUBLE PRECISION DEFAULT NULL, ADD location_longitude DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities DROP location_latitude, DROP location_longitude');
        $this->addSql('ALTER TABLE trips DROP destination_latitude, DROP destination_longitude');
    }
}
