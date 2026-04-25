<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260409213920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed an initial admin user for Back Office access.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "INSERT INTO users (username, email, roles, password, birthday, bio, profile_image, balance, face_registered, totp_enabled, totp_secret, created_at, updated_at)
             VALUES ('admin', 'admin@travelxp.local', '[\"ROLE_ADMIN\"]', '\$2y\$12\$yhP.7b0QJ1loznORILzbd..OIR3ac.P7RGbbYKU6WdwAycfBDY0m6', '2000-01-01', 'Default admin account', NULL, 0.00, 0, 0, NULL, NOW(), NOW())"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM users WHERE email = 'admin@travelxp.local'");
    }
}
