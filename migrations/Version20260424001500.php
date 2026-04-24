<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424001500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create login_history table for profile login audit trail.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE login_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ip_address VARCHAR(45) NOT NULL, country VARCHAR(120) DEFAULT NULL, region VARCHAR(120) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, isp VARCHAR(191) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, login_at DATETIME NOT NULL, INDEX IDX_LOGIN_HISTORY_USER (user_id), INDEX IDX_LOGIN_HISTORY_USER_TIME (user_id, login_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE login_history ADD CONSTRAINT FK_LOGIN_HISTORY_USER FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE login_history DROP FOREIGN KEY FK_LOGIN_HISTORY_USER');
        $this->addSql('DROP TABLE login_history');
    }
}
