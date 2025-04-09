<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    // src/Repository/CommandeRepository.php

public function countValidatedCommandsByUser(Users $user): int
{
    return $this->createQueryBuilder('c')
        ->select('count(c.id)')
        ->andWhere('c.user = :user')
        ->andWhere('c.status = :status')
        ->setParameter('user', $user)
        ->setParameter('status', 'Approuvé') // Assurez-vous que le statut correspond
        ->getQuery()
        ->getSingleScalarResult();
}
}