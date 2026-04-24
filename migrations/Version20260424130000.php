<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ensure property latitude/longitude columns exist for Geoapify property maps.';
    }

    public function up(Schema $schema): void
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['property'])) {
            return;
        }

        $columns = array_map(
            static fn ($column): string => strtolower($column->getName()),
            $this->connection->createSchemaManager()->listTableColumns('property')
        );

        if (!in_array('latitude', $columns, true)) {
            $this->addSql('ALTER TABLE property ADD latitude DOUBLE PRECISION DEFAULT NULL');
        }

        if (!in_array('longitude', $columns, true)) {
            $this->addSql('ALTER TABLE property ADD longitude DOUBLE PRECISION DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['property'])) {
            return;
        }

        $columns = array_map(
            static fn ($column): string => strtolower($column->getName()),
            $this->connection->createSchemaManager()->listTableColumns('property')
        );

        if (in_array('longitude', $columns, true)) {
            $this->addSql('ALTER TABLE property DROP longitude');
        }

        if (in_array('latitude', $columns, true)) {
            $this->addSql('ALTER TABLE property DROP latitude');
        }
    }
}
