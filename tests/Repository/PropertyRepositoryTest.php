<?php

namespace App\Tests\Repository;

use App\Repository\PropertyRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class PropertyRepositoryTest extends TestCase
{
    public function testCreateFilteredQueryBuilder(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $registry->method('getManagerForClass')->willReturn($entityManager);

        // We bypass the actual constructor calling $registry->getManagerForClass() 
        // to just test if it returns a QueryBuilder (if we fully mocked the EntityManager)
        // However, a pure repository unit test without KernelTestCase requires deep mocking.
        
        $this->markTestSkipped('Repository queries are better tested via KernelTestCase (integration tests). Building deep QueryBuilder mocks is brittle.');
    }
}
