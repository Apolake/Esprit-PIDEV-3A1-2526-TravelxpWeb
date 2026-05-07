<?php

namespace App\Tests\Entity;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BlogEntityTest extends TestCase
{
    private Blog $blog;
    private User $author;

    protected function setUp(): void
    {
        $this->blog = new Blog();
        $this->author = new User();
        $this->author->setEmail('author@test.com');
        $this->author->setUsername('author');
    }

    public function testBlogInitializesCollections(): void
    {
        $this->assertCount(0, $this->blog->getComments());
        $this->assertCount(0, $this->blog->getLikedByUsers());
        $this->assertCount(0, $this->blog->getDislikedByUsers());
    }

    public function testSetAndGetTitle(): void
    {
        $title = 'Test Blog Title';
        $this->blog->setTitle($title);
        $this->assertSame($title, $this->blog->getTitle());
    }

    public function testSetTitleTrimsWhitespace(): void
    {
        $this->blog->setTitle('  Title with spaces  ');
        $this->assertSame('Title with spaces', $this->blog->getTitle());
    }

    public function testSetAndGetContent(): void
    {
        $content = 'This is blog content.';
        $this->blog->setContent($content);
        $this->assertSame($content, $this->blog->getContent());
    }

    public function testSetContentTrimsWhitespace(): void
    {
        $this->blog->setContent('  Content  ');
        $this->assertSame('Content', $this->blog->getContent());
    }

    public function testSetAndGetImageUrl(): void
    {
        $url = 'https://example.com/image.jpg';
        $this->blog->setImageUrl($url);
        $this->assertSame($url, $this->blog->getImageUrl());
    }

    public function testEmptyImageUrlBecomesNull(): void
    {
        $this->blog->setImageUrl('');
        $this->assertNull($this->blog->getImageUrl());
    }

    public function testSetAndGetAuthor(): void
    {
        $this->blog->setAuthor($this->author);
        $this->assertSame($this->author, $this->blog->getAuthor());
    }

    public function testAddComment(): void
    {
        $comment = new Comment();
        $comment->setContent('Test comment');
        $this->blog->addComment($comment);

        $this->assertCount(1, $this->blog->getComments());
        $this->assertTrue($this->blog->getComments()->contains($comment));
        $this->assertSame($this->blog, $comment->getBlog());
    }

    public function testRemoveComment(): void
    {
        $comment = new Comment();
        $this->blog->addComment($comment);
        $this->blog->removeComment($comment);

        $this->assertCount(0, $this->blog->getComments());
    }

    public function testAddLikeBy(): void
    {
        $user = new User();
        $this->blog->addLikeBy($user);

        $this->assertTrue($this->blog->hasLikedBy($user));
        $this->assertFalse($this->blog->hasDislikedBy($user));
    }

    public function testAddDislikeBy(): void
    {
        $user = new User();
        $this->blog->addDislikeBy($user);

        $this->assertTrue($this->blog->hasDislikedBy($user));
        $this->assertFalse($this->blog->hasLikedBy($user));
    }

    public function testLikingRemovesDislikes(): void
    {
        $user = new User();
        $this->blog->addDislikeBy($user);
        $this->assertTrue($this->blog->hasDislikedBy($user));

        $this->blog->addLikeBy($user);
        $this->assertTrue($this->blog->hasLikedBy($user));
        $this->assertFalse($this->blog->hasDislikedBy($user));
    }

    public function testDislikingRemovesLikes(): void
    {
        $user = new User();
        $this->blog->addLikeBy($user);
        $this->assertTrue($this->blog->hasLikedBy($user));

        $this->blog->addDislikeBy($user);
        $this->assertTrue($this->blog->hasDislikedBy($user));
        $this->assertFalse($this->blog->hasLikedBy($user));
    }

    public function testGetLikesCount(): void
    {
        $user1 = new User();
        $user2 = new User();
        $this->blog->addLikeBy($user1);
        $this->blog->addLikeBy($user2);

        $this->assertSame(2, $this->blog->getLikesCount());
    }

    public function testGetDislikesCount(): void
    {
        $user1 = new User();
        $user2 = new User();
        $this->blog->addDislikeBy($user1);
        $this->blog->addDislikeBy($user2);

        $this->assertSame(2, $this->blog->getDislikesCount());
    }

    public function testOnPrePersistSetsTimestamps(): void
    {
        $this->blog->setTitle('Title');
        $this->blog->setContent('Content');
        $this->blog->onPrePersist();

        $this->assertNotNull($this->blog->getCreatedAt());
        $this->assertNotNull($this->blog->getUpdatedAt());
        $this->assertNotNull($this->blog->getPublishedAt());
    }

    public function testOnPreUpdateUpdatesTimestamp(): void
    {
        $before = new \DateTimeImmutable();
        $this->blog->onPrePersist();
        usleep(100);
        $this->blog->onPreUpdate();
        $after = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $this->blog->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->blog->getUpdatedAt());
    }
}
