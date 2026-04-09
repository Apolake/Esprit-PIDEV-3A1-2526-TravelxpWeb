<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[]
     */
    public function findByAdminFilters(?string $query, ?string $role, string $sortBy = 'createdAt', string $direction = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('u');

        $trimmedQuery = trim((string) $query);
        if ('' !== $trimmedQuery) {
            $search = '%'.mb_strtolower($trimmedQuery).'%';
            $orX = $qb->expr()->orX(
                'LOWER(u.username) LIKE :search',
                'LOWER(u.email) LIKE :search',
                'LOWER(COALESCE(u.bio, \'\')) LIKE :search'
            );

            if (ctype_digit($trimmedQuery)) {
                $orX->add('u.id = :idQuery');
                $qb->setParameter('idQuery', (int) $trimmedQuery);
            }

            $qb->andWhere($orX)->setParameter('search', $search);
        }

        if (in_array($role, ['ROLE_USER', 'ROLE_ADMIN'], true)) {
            $qb->andWhere('u.roles LIKE :role')->setParameter('role', '%"'.$role.'"%');
        }

        $sortable = [
            'username' => 'u.username',
            'email' => 'u.email',
            'birthday' => 'u.birthday',
            'createdAt' => 'u.createdAt',
            'updatedAt' => 'u.updatedAt',
        ];

        $orderBy = $sortable[$sortBy] ?? 'u.createdAt';
        $order = 'ASC' === strtoupper($direction) ? 'ASC' : 'DESC';

        return $qb
            ->orderBy($orderBy, $order)
            ->addOrderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
