<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260409221110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add database-backed gamification schema and seed quests/admin-controllable stats.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD xp INT DEFAULT 0 NOT NULL, ADD level INT DEFAULT 1 NOT NULL, ADD streak INT DEFAULT 0 NOT NULL');

        $this->addSql('CREATE TABLE quests (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(140) NOT NULL, description LONGTEXT DEFAULT NULL, goal INT NOT NULL, reward_xp INT DEFAULT 100 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_quest_progress (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quest_id INT NOT NULL, progress INT DEFAULT 0 NOT NULL, status VARCHAR(20) DEFAULT \'active\' NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_28E23204A76ED395 (user_id), INDEX IDX_28E2320427DACF50 (quest_id), UNIQUE INDEX uniq_user_quest (user_id, quest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_quest_progress ADD CONSTRAINT FK_28E23204A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_quest_progress ADD CONSTRAINT FK_28E2320427DACF50 FOREIGN KEY (quest_id) REFERENCES quests (id) ON DELETE CASCADE');

        $this->addSql("INSERT INTO quests (title, description, goal, reward_xp, is_active, created_at, updated_at) VALUES
            ('Complete profile setup', 'Fill profile basics including bio and birthday.', 1, 100, 1, NOW(), NOW()),
            ('Update profile 3 times', 'Keep your profile fresh and active.', 3, 180, 1, NOW(), NOW()),
            ('Log in 5 days in a row', 'Build a consistent TravelXP streak.', 5, 250, 1, NOW(), NOW()),
            ('Reach level 10', 'Accumulate enough XP to hit level 10.', 10, 400, 1, NOW(), NOW())");

        $this->addSql('UPDATE users SET xp = ((id * 173) % 1600) + 300, level = FLOOR((((id * 173) % 1600) + 300) / 250) + 1, streak = (id % 8) + 2');

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

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_quest_progress');
        $this->addSql('DROP TABLE quests');
        $this->addSql('ALTER TABLE users DROP xp, DROP level, DROP streak');
    }
}
