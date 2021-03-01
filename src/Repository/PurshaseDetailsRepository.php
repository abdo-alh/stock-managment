<?php

namespace App\Repository;

use App\Entity\PurshaseDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PurshaseDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurshaseDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurshaseDetails[]    findAll()
 * @method PurshaseDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurshaseDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurshaseDetails::class);
    }

    // /**
    //  * @return PurshaseDetails[] Returns an array of PurshaseDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PurshaseDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
