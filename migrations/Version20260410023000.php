<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260410023000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trips and activities modules with participant pivot tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE trips (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, trip_name VARCHAR(255) NOT NULL, origin VARCHAR(255) DEFAULT NULL, destination VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(50) NOT NULL, budget_amount DOUBLE PRECISION DEFAULT NULL, currency VARCHAR(10) NOT NULL, total_expenses DOUBLE PRECISION DEFAULT NULL, total_xp_earned INT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, cover_image_url VARCHAR(255) DEFAULT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8D93D6496BF700BD (status), INDEX IDX_8D93D649E0C43F19 (destination), INDEX IDX_8D93D649727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE activities (id INT AUTO_INCREMENT NOT NULL, trip_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, activity_date DATE DEFAULT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, location_name VARCHAR(255) DEFAULT NULL, transport_type VARCHAR(100) DEFAULT NULL, cost_amount DOUBLE PRECISION DEFAULT NULL, currency VARCHAR(10) NOT NULL, xp_earned INT DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A8FBF37FB51A5FA (trip_id), INDEX IDX_A8FBF37F6BF700BD (status), INDEX IDX_A8FBF37F2D7D22D3 (activity_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trip_participants (trip_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8AD400365B51A5FA (trip_id), INDEX IDX_8AD40036A76ED395 (user_id), PRIMARY KEY(trip_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trip_activity_participants (activity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DD3C4D4581C06096 (activity_id), INDEX IDX_DD3C4D45A76ED395 (user_id), PRIMARY KEY(activity_id, user_id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE trips ADD CONSTRAINT FK_8D93D649727ACA70 FOREIGN KEY (parent_id) REFERENCES trips (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_A8FBF37FB51A5FA FOREIGN KEY (trip_id) REFERENCES trips (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE trip_participants ADD CONSTRAINT FK_8AD400365B51A5FA FOREIGN KEY (trip_id) REFERENCES trips (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_participants ADD CONSTRAINT FK_8AD40036A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_activity_participants ADD CONSTRAINT FK_DD3C4D4581C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_activity_participants ADD CONSTRAINT FK_DD3C4D45A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trips DROP FOREIGN KEY FK_8D93D649727ACA70');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_A8FBF37FB51A5FA');
        $this->addSql('ALTER TABLE trip_participants DROP FOREIGN KEY FK_8AD400365B51A5FA');
        $this->addSql('ALTER TABLE trip_participants DROP FOREIGN KEY FK_8AD40036A76ED395');
        $this->addSql('ALTER TABLE trip_activity_participants DROP FOREIGN KEY FK_DD3C4D4581C06096');
        $this->addSql('ALTER TABLE trip_activity_participants DROP FOREIGN KEY FK_DD3C4D45A76ED395');

        $this->addSql('DROP TABLE trip_activity_participants');
        $this->addSql('DROP TABLE trip_participants');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE trips');
    }
}
