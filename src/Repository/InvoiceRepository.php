<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\Purshase;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    // /**
    //  * @return Invoice[] Returns an array of Invoice objects
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

    public function findAll()
    {
        return $this->findBy(array(), array('invoice_date' => 'DESC'));
    }

    public function getTodayInvoices()
    {
        $qb = $this->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('DAY(i.invoice_date) = :day')
            ->setParameter('day', date('j'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getAllInvoices()
    {
        $qb = $this->createQueryBuilder('i')
                   ->orderBy('i.invoice_date','DESC')
                   ->setMaxResults(10);
        return $qb->getQuery()->getResult();
    }

    public function getMonthInvoices()
    {
        $qb = $this->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('MONTH(i.invoice_date) = :month')
            ->setParameter('month',  date('m'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getMonthllyRevenu()
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.amount)')
            ->where('MONTH(i.invoice_date) = :month')
            ->setParameter('month',  date('m'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getMonthlly()
    {
        $qb = $this->createQueryBuilder('i')
            ->select('MONTH(i.invoice_date) as invoice_month,SUM(i.amount) as amount_invoices,SUM(d.quantity) as count_products')
            ->join('i.invoiceDetails','d')
            ->groupBy('invoice_month')
            ->orderBy('invoice_month')
        ;
        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Invoice
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
