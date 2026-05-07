<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417012000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional image_url column to activities table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities ADD image_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities DROP image_url');
    }
}
