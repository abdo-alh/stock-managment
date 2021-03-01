<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\InvoiceDetails;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method InvoiceDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceDetails[]    findAll()
 * @method InvoiceDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceDetails::class);
    }

    public function getMostProductsSales()
    {
        $qb = $this->createQueryBuilder('d')
                   ->select('d.productName, SUM(d.quantity) AS TotalQuantity')
                   ->groupBy('d.productName')
                   ->orderBy('SUM(d.quantity)','DESC')
                   ->setMaxResults(10);
        return $qb->getQuery()->getResult();
    }

    public function getTodaySales()
    {
        $qb = $this->createQueryBuilder('d')
            ->select('SUM(d.quantity)')
            ->join('d.invoice','i')
            ->where('DAY(i.invoice_date) = :day')
            ->setParameter('day', date('j'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getMonthSales()
    {
        $qb = $this->createQueryBuilder('d')
            ->select('SUM(d.quantity)')
            ->join('d.invoice','i')
            ->where('MONTH(i.invoice_date) = :month')
            ->setParameter('month',  date('m'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    // /**
    //  * @return InvoiceDetails[] Returns an array of InvoiceDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvoiceDetails
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
