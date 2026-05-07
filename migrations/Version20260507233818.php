<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Doctrine Doctor remediation migration:
 * - Add created_by_id / updated_by_id (blameable) columns to all entity tables
 * - Convert float columns to decimal for monetary/coordinate precision
 * - Add FK constraints for blameable columns with _id suffix convention
 */
final class Version20260507233818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Doctrine Doctor remediation: add blameable fields with _id suffix, fix decimal types.';
    }

    public function up(Schema $schema): void
    {
        // Disable FK checks globally to prevent ordering issues
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // =====================================================
        // 1. Add blameable columns (created_by_id, updated_by_id)
        // =====================================================
        $blameableTables = [
            'activities', 'activity_waiting_list', 'blog_comments', 'blogs',
            'booking', 'budgets', 'expense_entries', 'notifications',
            'offer', 'payments', 'property', 'quests', 'service',
            'trip_waiting_list', 'trips', 'users',
        ];

        foreach ($blameableTables as $table) {
            $this->addSql("ALTER TABLE `{$table}` ADD COLUMN IF NOT EXISTS `created_by_id` INT DEFAULT NULL");
            $this->addSql("ALTER TABLE `{$table}` ADD COLUMN IF NOT EXISTS `updated_by_id` INT DEFAULT NULL");
            $this->addSql("ALTER TABLE `{$table}` ADD INDEX IF NOT EXISTS `idx_{$table}_created_by_id` (`created_by_id`)");
            $this->addSql("ALTER TABLE `{$table}` ADD INDEX IF NOT EXISTS `idx_{$table}_updated_by_id` (`updated_by_id`)");
            $this->addSql("ALTER TABLE `{$table}` ADD CONSTRAINT `fk_{$table}_created_by_id` FOREIGN KEY IF NOT EXISTS (`created_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
            $this->addSql("ALTER TABLE `{$table}` ADD CONSTRAINT `fk_{$table}_updated_by_id` FOREIGN KEY IF NOT EXISTS (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
        }

        // =====================================================
        // 2. Fix decimal types for monetary/coordinate columns
        // =====================================================
        $this->addSql('ALTER TABLE activities MODIFY cost_amount DECIMAL(12,2) DEFAULT NULL');
        $this->addSql('ALTER TABLE activities MODIFY location_latitude DECIMAL(10,7) DEFAULT NULL');
        $this->addSql('ALTER TABLE activities MODIFY location_longitude DECIMAL(10,7) DEFAULT NULL');
        $this->addSql('ALTER TABLE trips MODIFY budget_amount DECIMAL(12,2) DEFAULT NULL');
        $this->addSql('ALTER TABLE trips MODIFY total_expenses DECIMAL(12,2) DEFAULT NULL');
        $this->addSql('ALTER TABLE service MODIFY price DECIMAL(10,2) NOT NULL');

        // Re-enable FK checks
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        $blameableTables = [
            'activities', 'activity_waiting_list', 'blog_comments', 'blogs',
            'booking', 'budgets', 'expense_entries', 'notifications',
            'offer', 'payments', 'property', 'quests', 'service',
            'trip_waiting_list', 'trips', 'users',
        ];

        foreach ($blameableTables as $table) {
            $this->addSql("ALTER TABLE `{$table}` DROP FOREIGN KEY IF EXISTS `fk_{$table}_created_by_id`");
            $this->addSql("ALTER TABLE `{$table}` DROP FOREIGN KEY IF EXISTS `fk_{$table}_updated_by_id`");
            $this->addSql("ALTER TABLE `{$table}` DROP INDEX IF EXISTS `idx_{$table}_created_by_id`");
            $this->addSql("ALTER TABLE `{$table}` DROP INDEX IF EXISTS `idx_{$table}_updated_by_id`");
            $this->addSql("ALTER TABLE `{$table}` DROP COLUMN IF EXISTS `created_by_id`");
            $this->addSql("ALTER TABLE `{$table}` DROP COLUMN IF EXISTS `updated_by_id`");
        }

        $this->addSql('ALTER TABLE activities MODIFY cost_amount DOUBLE DEFAULT NULL');
        $this->addSql('ALTER TABLE activities MODIFY location_latitude DOUBLE DEFAULT NULL');
        $this->addSql('ALTER TABLE activities MODIFY location_longitude DOUBLE DEFAULT NULL');
        $this->addSql('ALTER TABLE trips MODIFY budget_amount DOUBLE DEFAULT NULL');
        $this->addSql('ALTER TABLE trips MODIFY total_expenses DOUBLE DEFAULT NULL');
        $this->addSql('ALTER TABLE service MODIFY price DOUBLE DEFAULT NULL');

        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
}
