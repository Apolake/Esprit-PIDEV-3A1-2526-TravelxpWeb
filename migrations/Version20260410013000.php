<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260410013000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add property, offer, service and booking schema for front/back office CRUD modules.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE properties (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, description LONGTEXT DEFAULT NULL, property_type VARCHAR(80) NOT NULL, city VARCHAR(120) NOT NULL, country VARCHAR(120) NOT NULL, address VARCHAR(255) DEFAULT NULL, price_per_night NUMERIC(10, 2) NOT NULL, bedrooms INT NOT NULL, max_guests INT NOT NULL, images VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A367D338AA9E377A (property_type), INDEX IDX_A367D3388BAC62AF (is_active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offers (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, title VARCHAR(180) NOT NULL, description LONGTEXT DEFAULT NULL, discount_percentage NUMERIC(5, 2) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_29BDB34FA76ED395 (property_id), INDEX IDX_29BDB34F8BAC62AF (is_active), INDEX IDX_29BDB34F6CA2CA6C (start_date), INDEX IDX_29BDB34FBCE335E1 (end_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, provider_name VARCHAR(140) NOT NULL, service_type VARCHAR(80) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, is_available TINYINT(1) NOT NULL, eco_friendly TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E19D9AD7CFE6AD47 (service_type), INDEX IDX_E19D9AD7313D6D9D (is_available), INDEX IDX_E19D9AD7444E2CF7 (eco_friendly), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bookings (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, user_id INT NOT NULL, booking_date DATE NOT NULL, duration INT NOT NULL, total_price NUMERIC(10, 2) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E96F8F5EA76ED395 (property_id), INDEX IDX_E96F8F5EA76ED395A76ED395 (property_id, booking_date), INDEX IDX_E96F8F5E6CA2CA6C (booking_date), INDEX IDX_E96F8F5E6BF700BD (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE booking_services (booking_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_A04D2EF53301C60F (booking_id), INDEX IDX_A04D2EF5ED5CA9E6 (service_id), PRIMARY KEY(booking_id, service_id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_29BDB34FA76ED395 FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_E96F8F5EA76ED395 FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking_services ADD CONSTRAINT FK_A04D2EF53301C60F FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking_services ADD CONSTRAINT FK_A04D2EF5ED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_29BDB34FA76ED395');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_E96F8F5EA76ED395');
        $this->addSql('ALTER TABLE booking_services DROP FOREIGN KEY FK_A04D2EF53301C60F');
        $this->addSql('ALTER TABLE booking_services DROP FOREIGN KEY FK_A04D2EF5ED5CA9E6');

        $this->addSql('DROP TABLE booking_services');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE offers');
        $this->addSql('DROP TABLE properties');
    }
}
