<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findSomeProducts($value)
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.name = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findProductsByName($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name like :value')
            ->setParameter('value', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findProductLowStock()
    {
        return $this->createQueryBuilder('p')
            ->where('p.quantity <= :value')
            ->setParameter('value', 5)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product 
     */
    public function findProductByName($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name like :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product 
     */
    public function findProductByNameV2($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name like :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
