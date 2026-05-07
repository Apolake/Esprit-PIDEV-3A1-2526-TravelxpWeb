<?php

namespace App\Tests\Entity;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentEntityTest extends TestCase
{
    private Comment $comment;
    private Blog $blog;
    private User $author;

    protected function setUp(): void
    {
        $this->comment = new Comment();
        $this->blog = new Blog();
        $this->author = new User();
        $this->author->setEmail('author@test.com');
        $this->author->setUsername('author');
    }

    public function testCommentInitializesCollections(): void
    {
        $this->assertCount(0, $this->comment->getLikedByUsers());
        $this->assertCount(0, $this->comment->getDislikedByUsers());
    }

    public function testSetAndGetBlog(): void
    {
        $this->comment->setBlog($this->blog);
        $this->assertSame($this->blog, $this->comment->getBlog());
    }

    public function testSetAndGetAuthor(): void
    {
        $this->comment->setAuthor($this->author);
        $this->assertSame($this->author, $this->comment->getAuthor());
    }

    public function testSetAndGetContent(): void
    {
        $content = 'This is a comment.';
        $this->comment->setContent($content);
        $this->assertSame($content, $this->comment->getContent());
    }

    public function testSetContentTrimsWhitespace(): void
    {
        $this->comment->setContent('  Comment text  ');
        $this->assertSame('Comment text', $this->comment->getContent());
    }

    public function testAddLikeBy(): void
    {
        $user = new User();
        $this->comment->addLikeBy($user);

        $this->assertTrue($this->comment->hasLikedBy($user));
        $this->assertFalse($this->comment->hasDislikedBy($user));
    }

    public function testAddDislikeBy(): void
    {
        $user = new User();
        $this->comment->addDislikeBy($user);

        $this->assertTrue($this->comment->hasDislikedBy($user));
        $this->assertFalse($this->comment->hasLikedBy($user));
    }

    public function testLikingRemovesDislikes(): void
    {
        $user = new User();
        $this->comment->addDislikeBy($user);
        $this->assertTrue($this->comment->hasDislikedBy($user));

        $this->comment->addLikeBy($user);
        $this->assertTrue($this->comment->hasLikedBy($user));
        $this->assertFalse($this->comment->hasDislikedBy($user));
    }

    public function testDislikingRemovesLikes(): void
    {
        $user = new User();
        $this->comment->addLikeBy($user);
        $this->assertTrue($this->comment->hasLikedBy($user));

        $this->comment->addDislikeBy($user);
        $this->assertTrue($this->comment->hasDislikedBy($user));
        $this->assertFalse($this->comment->hasLikedBy($user));
    }

    public function testGetLikesCount(): void
    {
        $user1 = new User();
        $user2 = new User();
        $this->comment->addLikeBy($user1);
        $this->comment->addLikeBy($user2);

        $this->assertSame(2, $this->comment->getLikesCount());
    }

    public function testGetDislikesCount(): void
    {
        $user1 = new User();
        $user2 = new User();
        $this->comment->addDislikeBy($user1);
        $this->comment->addDislikeBy($user2);

        $this->assertSame(2, $this->comment->getDislikesCount());
    }

    public function testOnPrePersistSetsTimestamps(): void
    {
        $this->comment->setBlog($this->blog);
        $this->comment->setAuthor($this->author);
        $this->comment->setContent('Test comment content.');
        $this->comment->onPrePersist();

        $this->assertNotNull($this->comment->getCreatedAt());
        $this->assertNotNull($this->comment->getUpdatedAt());
    }

    public function testOnPreUpdateUpdatesTimestamp(): void
    {
        $before = new \DateTimeImmutable();
        $this->comment->onPrePersist();
        usleep(100);
        $this->comment->onPreUpdate();
        $after = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $this->comment->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->comment->getUpdatedAt());
    }
}
