<?php

namespace App\Repository;

use App\Entity\Purshase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Purshase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purshase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purshase[]    findAll()
 * @method Purshase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurshaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purshase::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('purshase_date' => 'DESC'));
    }

    public function getMonthllyPurshases()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('MONTH(p.purshase_date) as purchase_month,SUM(p.amount) as amount_purchases,SUM(d.quantity) as count_products')
            ->join('p.purshaseDetails','d')
            ->groupBy('purchase_month')
            ->orderBy('purchase_month')
        ;
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Purshase[] Returns an array of Purshase objects
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
    public function findOneBySomeField($value): ?Purshase
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
