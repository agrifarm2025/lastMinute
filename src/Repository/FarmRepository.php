<?php

namespace App\Repository;

use App\Entity\Farm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Farm>
 */
class FarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }
    public function getFieldJoin($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT  b 
                 FROM App\Entity\Farm a 
                 INNER JOIN App\Entity\Field b 
                 WITH a.id = b.Farm 
                 WHERE a.id = :id'
            )
            ->setParameter('id', $id)
            ->getResult();
    }

    /**
     * Find all farms belonging to a specific user
     * @param int $userId The ID of the user
     * @return Farm[] Returns an array of Farm objects
     */
    public function findByUserId($userId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Farm[] Returns an array of Farm objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Farm
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
