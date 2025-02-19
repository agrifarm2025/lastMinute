<?php

namespace App\Repository;

use App\Entity\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Field>
 */
class FieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Field::class);
    }

    public function getToDo(Field $field)
    {
        return $this->getEntityManager()
        ->createQuery(
            "SELECT b 
             FROM App\Entity\Field a 
             INNER JOIN App\Entity\Task b 
             WITH a.id = b.field 
             WHERE a.id = :id 
             AND b.status = 'to do'
             ORDER BY 
                 b.date ASC, 
                 CASE b.priority
                     WHEN 'High' THEN 1
                     WHEN 'Medium' THEN 2
                     WHEN 'Low' THEN 3
                     ELSE 4  
                 END DESC   
       " )
        ->setParameter('id', $field)
        ->getResult();
        
    }

    public function getInProgress(Field $field)
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT b 
                 FROM App\Entity\Field a 
                 INNER JOIN App\Entity\Task b 
                 WITH a.id = b.field  
                 WHERE a.id = :id 
                 AND b.status = :status
                 ORDER BY 
                 CASE b.priority
                     WHEN 'High' THEN 1
                     WHEN 'Medium' THEN 2
                     WHEN 'Low' THEN 3
                     ELSE 4  
                 END DESC "
            )
            ->setParameter('id', $field)
            ->setParameter('status', 'In progres')
            ->getResult();
    }

    public function getDone(Field $field)
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT b 
                 FROM App\Entity\Field a 
                 INNER JOIN App\Entity\Task b 
                 WITH a.id = b.field  
                 WHERE a.id = :id 
                 AND b.status = :status
                 ORDER BY 
                 CASE b.priority
                     WHEN 'High' THEN 1
                     WHEN 'Medium' THEN 2
                     WHEN 'Low' THEN 3
                     ELSE 4  
                 END DESC "
            )
            ->setParameter('id', $field)
            ->setParameter('status', 'done')
            ->getResult();
    }
   /* public function somme(Field $field)
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT COALESCE(SUM(t.total), 0) 
                 FROM App\Entity\Task t 
                 WHERE t.field = :field"
            )
            ->setParameter('field', $field)
            ->getSingleScalarResult(); // Ensures a single numeric value is returned
    }*/
    

    //    /**
    //     * @return Field[] Returns an array of Field objects
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

    //    public function findOneBySomeField($value): ?Field
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
