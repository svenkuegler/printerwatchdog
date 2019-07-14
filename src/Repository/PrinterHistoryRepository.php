<?php

namespace App\Repository;

use App\Entity\PrinterHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PrinterHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrinterHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrinterHistory[]    findAll()
 * @method PrinterHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrinterHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PrinterHistory::class);
    }

    // /**
    //  * @return PrinterHistory[] Returns an array of PrinterHistory objects
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
    public function findOneBySomeField($value): ?PrinterHistory
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
