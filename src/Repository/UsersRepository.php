<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Users>
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findNonAdminUsers()
{
    return $this->createQueryBuilder('u')
        ->where("u.roles NOT LIKE :role")
        ->setParameter('role', '%ROLE_ADMIN%') // Exclude users with ROLE_ADMIN
        ->getQuery()
        ->getResult();
}

public function add(Users $user, bool $flush = false): void
{
    $this->getEntityManager()->persist($user);

    if ($flush) {
        $this->getEntityManager()->flush();
    }
}

public function findByGoogleIdOrEmail(?string $googleId, string $email): ?Users
{
    return $this->createQueryBuilder('u')
        ->where('u.google_id = :googleId OR u.email = :email')
        ->setParameter('googleId', $googleId)
        ->setParameter('email', $email)
        ->getQuery()
        ->getOneOrNullResult();
}

public function save(Users $user, bool $flush = true): void
{
    $entityManager = $this->getEntityManager(); // Get the entity manager
    $entityManager->persist($user);

    if ($flush) {
        $entityManager->flush();
    }
}




    //    /**
    //     * @return Users[] Returns an array of Users objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Users
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
