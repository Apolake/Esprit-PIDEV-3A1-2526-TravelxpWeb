<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260423193000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Firebase UID, TOTP recovery codes, payments, budgets and expense entries.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD firebase_uid VARCHAR(128) DEFAULT NULL, ADD totp_recovery_codes JSON DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_users_firebase_uid ON users (firebase_uid)');

        $this->addSql('CREATE TABLE budgets (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(180) NOT NULL, destination VARCHAR(180) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, planned_amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6FEA0C8AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE expense_entries (id INT AUTO_INCREMENT NOT NULL, budget_id INT NOT NULL, title VARCHAR(180) NOT NULL, category VARCHAR(30) NOT NULL, amount NUMERIC(10, 2) NOT NULL, expense_date DATE NOT NULL, note LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F1945B5BBAC19C55 (budget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, booking_id INT DEFAULT NULL, budget_id INT DEFAULT NULL, stripe_payment_intent_id VARCHAR(191) NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(40) NOT NULL, failure_message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX uniq_payment_intent (stripe_payment_intent_id), INDEX IDX_65D29B32A76ED395 (user_id), INDEX IDX_65D29B323301C60F (booking_id), INDEX IDX_65D29B32BAC19C55 (budget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE budgets ADD CONSTRAINT FK_6FEA0C8AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_entries ADD CONSTRAINT FK_F1945B5BBAC19C55 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B323301C60F FOREIGN KEY (booking_id) REFERENCES booking (booking_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32BAC19C55 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense_entries DROP FOREIGN KEY FK_F1945B5BBAC19C55');
        $this->addSql('ALTER TABLE budgets DROP FOREIGN KEY FK_6FEA0C8AA76ED395');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32A76ED395');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B323301C60F');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32BAC19C55');

        $this->addSql('DROP TABLE expense_entries');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE budgets');

        $this->addSql('DROP INDEX uniq_users_firebase_uid ON users');
        $this->addSql('ALTER TABLE users DROP firebase_uid, DROP totp_recovery_codes');
    }
}
