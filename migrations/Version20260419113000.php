<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260419113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Repair missing gamification schema objects when database drift caused quests tables/columns to disappear.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        $usersColumns = array_map(
            static fn ($column): string => strtolower($column->getName()),
            $schemaManager->listTableColumns('users')
        );

        if (!in_array('xp', $usersColumns, true)) {
            $this->addSql('ALTER TABLE users ADD xp INT DEFAULT 0 NOT NULL');
        }

        if (!in_array('level', $usersColumns, true)) {
            $this->addSql('ALTER TABLE users ADD level INT DEFAULT 1 NOT NULL');
        }

        if (!in_array('streak', $usersColumns, true)) {
            $this->addSql('ALTER TABLE users ADD streak INT DEFAULT 0 NOT NULL');
        }

        if (!$schemaManager->tablesExist(['quests'])) {
            $this->addSql('CREATE TABLE quests (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(140) NOT NULL, description LONGTEXT DEFAULT NULL, goal INT NOT NULL, reward_xp INT DEFAULT 100 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$schemaManager->tablesExist(['user_quest_progress'])) {
            $this->addSql("CREATE TABLE user_quest_progress (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quest_id INT NOT NULL, progress INT DEFAULT 0 NOT NULL, status VARCHAR(20) DEFAULT 'active' NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A8DE92BAA76ED395 (user_id), INDEX IDX_A8DE92BA209E9EF4 (quest_id), UNIQUE INDEX uniq_user_quest (user_id, quest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            $this->addSql('ALTER TABLE user_quest_progress ADD CONSTRAINT FK_A8DE92BAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE user_quest_progress ADD CONSTRAINT FK_A8DE92BA209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id) ON DELETE CASCADE');
        }

        $questsCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM quests');
        if ($questsCount === 0) {
            $this->addSql("INSERT INTO quests (title, description, goal, reward_xp, is_active, created_at, updated_at) VALUES
                ('Complete profile setup', 'Fill profile basics including bio and birthday.', 1, 100, 1, NOW(), NOW()),
                ('Update profile 3 times', 'Keep your profile fresh and active.', 3, 180, 1, NOW(), NOW()),
                ('Log in 5 days in a row', 'Build a consistent TravelXP streak.', 5, 250, 1, NOW(), NOW()),
                ('Reach level 10', 'Accumulate enough XP to hit level 10.', 10, 400, 1, NOW(), NOW())");
        }

        $userQuestCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM user_quest_progress');
        if ($userQuestCount === 0) {
            $this->addSql("INSERT INTO user_quest_progress (user_id, quest_id, progress, status, updated_at)
                SELECT
                    u.id,
                    q.id,
                    CASE
                        WHEN q.goal = 1 THEN 1
                        ELSE LEAST(q.goal, ((u.id * q.id) % q.goal) + IF(q.goal > 2, 1, 0))
                    END AS progress_value,
                    CASE
                        WHEN (
                            CASE
                                WHEN q.goal = 1 THEN 1
                                ELSE LEAST(q.goal, ((u.id * q.id) % q.goal) + IF(q.goal > 2, 1, 0))
                            END
                        ) >= q.goal THEN 'completed'
                        ELSE 'active'
                    END AS progress_status,
                    NOW()
                FROM users u
                CROSS JOIN quests q");
        }
    }

    public function down(Schema $schema): void
    {
        // Intentional no-op: repair migration should not drop live data on rollback.
    }
}
