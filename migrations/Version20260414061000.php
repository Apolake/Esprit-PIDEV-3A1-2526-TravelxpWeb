<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260414061000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add blog and comment modules with user reactions.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE blogs (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(180) NOT NULL, content LONGTEXT NOT NULL, image_url VARCHAR(500) DEFAULT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BA36E208F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, blog_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CDA51A5FA (blog_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE blog_likes (blog_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_38903A9BDA51A5FA (blog_id), INDEX IDX_38903A9BA76ED395 (user_id), PRIMARY KEY(blog_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_dislikes (blog_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D95E3AB5DA51A5FA (blog_id), INDEX IDX_D95E3AB5A76ED395 (user_id), PRIMARY KEY(blog_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_likes (comment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6648D5D4F8697D13 (comment_id), INDEX IDX_6648D5D4A76ED395 (user_id), PRIMARY KEY(comment_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_dislikes (comment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2C3818E9F8697D13 (comment_id), INDEX IDX_2C3818E9A76ED395 (user_id), PRIMARY KEY(comment_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE blogs ADD CONSTRAINT FK_BA36E208F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_9474526CDA51A5FA FOREIGN KEY (blog_id) REFERENCES blogs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE blog_likes ADD CONSTRAINT FK_38903A9BDA51A5FA FOREIGN KEY (blog_id) REFERENCES blogs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_likes ADD CONSTRAINT FK_38903A9BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_dislikes ADD CONSTRAINT FK_D95E3AB5DA51A5FA FOREIGN KEY (blog_id) REFERENCES blogs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_dislikes ADD CONSTRAINT FK_D95E3AB5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_likes ADD CONSTRAINT FK_6648D5D4F8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_likes ADD CONSTRAINT FK_6648D5D4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_dislikes ADD CONSTRAINT FK_2C3818E9F8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_dislikes ADD CONSTRAINT FK_2C3818E9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment_dislikes DROP FOREIGN KEY FK_2C3818E9F8697D13');
        $this->addSql('ALTER TABLE comment_dislikes DROP FOREIGN KEY FK_2C3818E9A76ED395');
        $this->addSql('ALTER TABLE comment_likes DROP FOREIGN KEY FK_6648D5D4F8697D13');
        $this->addSql('ALTER TABLE comment_likes DROP FOREIGN KEY FK_6648D5D4A76ED395');
        $this->addSql('ALTER TABLE blog_dislikes DROP FOREIGN KEY FK_D95E3AB5DA51A5FA');
        $this->addSql('ALTER TABLE blog_dislikes DROP FOREIGN KEY FK_D95E3AB5A76ED395');
        $this->addSql('ALTER TABLE blog_likes DROP FOREIGN KEY FK_38903A9BDA51A5FA');
        $this->addSql('ALTER TABLE blog_likes DROP FOREIGN KEY FK_38903A9BA76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_9474526CDA51A5FA');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE blogs DROP FOREIGN KEY FK_BA36E208F675F31B');

        $this->addSql('DROP TABLE comment_dislikes');
        $this->addSql('DROP TABLE comment_likes');
        $this->addSql('DROP TABLE blog_dislikes');
        $this->addSql('DROP TABLE blog_likes');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE blogs');
    }
}
