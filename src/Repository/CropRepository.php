<?php

namespace App\Repository;

use App\Entity\Crop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Crop>
 */
class CropRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Crop::class);
    }
    public function getCropJoin($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT  b 
                 FROM App\Entity\Crop a 
                 INNER JOIN App\Entity\Soildata b 
                 WITH a.id = b.crop 
                 WHERE a.id = :id'
            )
            ->setParameter('id', $id)
            ->getResult();
    }

    //    /**
    //     * @return Crop[] Returns an array of Crop objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Crop
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
