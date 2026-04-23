<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260418010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trip/activity capacity fields, waiting-list tables, and notifications table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trips ADD total_capacity INT NOT NULL DEFAULT 1, ADD available_seats INT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE activities ADD total_capacity INT NOT NULL DEFAULT 1, ADD available_seats INT NOT NULL DEFAULT 1');

        $this->addSql('UPDATE trips t SET t.total_capacity = GREATEST(1, (SELECT COUNT(*) FROM trip_participants tp WHERE tp.trip_id = t.id))');
        $this->addSql('UPDATE trips t SET t.available_seats = GREATEST(0, t.total_capacity - (SELECT COUNT(*) FROM trip_participants tp WHERE tp.trip_id = t.id))');

        $this->addSql('UPDATE activities a SET a.total_capacity = GREATEST(1, (SELECT COUNT(*) FROM trip_activity_participants ap WHERE ap.activity_id = a.id))');
        $this->addSql('UPDATE activities a SET a.available_seats = GREATEST(0, a.total_capacity - (SELECT COUNT(*) FROM trip_activity_participants ap WHERE ap.activity_id = a.id))');

        $this->addSql("CREATE TABLE trip_waiting_list (id INT AUTO_INCREMENT NOT NULL, trip_id BIGINT NOT NULL, user_id INT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'WAITING', queued_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL, promoted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2CA7E4A55B51A5FA (trip_id), INDEX IDX_2CA7E4A5A76ED395 (user_id), INDEX idx_trip_waiting_status_exp (status, expires_at), UNIQUE INDEX uniq_trip_waiting_trip_user_status (trip_id, user_id, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
        $this->addSql("CREATE TABLE activity_waiting_list (id INT AUTO_INCREMENT NOT NULL, activity_id BIGINT NOT NULL, user_id INT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'WAITING', queued_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL, promoted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3A4D3D4F81C06096 (activity_id), INDEX IDX_3A4D3D4FA76ED395 (user_id), INDEX idx_activity_waiting_status_exp (status, expires_at), UNIQUE INDEX uniq_activity_waiting_activity_user_status (activity_id, user_id, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(80) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, context JSON DEFAULT NULL, is_read TINYINT(1) NOT NULL DEFAULT 0, read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), INDEX idx_notifications_user_read_created (user_id, is_read, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE trip_waiting_list ADD CONSTRAINT FK_2CA7E4A55B51A5FA FOREIGN KEY (trip_id) REFERENCES trips (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_waiting_list ADD CONSTRAINT FK_2CA7E4A5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_waiting_list ADD CONSTRAINT FK_3A4D3D4F81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_waiting_list ADD CONSTRAINT FK_3A4D3D4FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip_waiting_list DROP FOREIGN KEY FK_2CA7E4A55B51A5FA');
        $this->addSql('ALTER TABLE trip_waiting_list DROP FOREIGN KEY FK_2CA7E4A5A76ED395');
        $this->addSql('ALTER TABLE activity_waiting_list DROP FOREIGN KEY FK_3A4D3D4F81C06096');
        $this->addSql('ALTER TABLE activity_waiting_list DROP FOREIGN KEY FK_3A4D3D4FA76ED395');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');

        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE activity_waiting_list');
        $this->addSql('DROP TABLE trip_waiting_list');

        $this->addSql('ALTER TABLE activities DROP total_capacity, DROP available_seats');
        $this->addSql('ALTER TABLE trips DROP total_capacity, DROP available_seats');
    }
}
