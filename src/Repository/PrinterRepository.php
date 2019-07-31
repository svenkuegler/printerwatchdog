<?php

namespace App\Repository;

use App\Entity\Printer;
use App\Entity\PrinterSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Printer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Printer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Printer[]    findAll()
 * @method Printer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrinterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Printer::class);
    }

    /**
     * @return PrinterSummary
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSummary()
    {
        $summary = new PrinterSummary();

        $summary->setLastCheck(
            $this->createQueryBuilder('p')
                ->select('MAX(p.lastCheck) as lastCheck')
                ->getQuery()->getOneOrNullResult()['lastCheck']
        );

        $summary->setRecentlyUnreachable(
            (int)$this->createQueryBuilder('p')
                ->andWhere('p.unreachableCount > 0')
                ->select('COUNT(p.id) as unreachable')
                ->getQuery()->getOneOrNullResult()['unreachable']
        );

        $summary->setPrinterTotalSw(
            (int)$this->createQueryBuilder('p')
                ->andWhere('p.isColorPrinter = 0')
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $summary->setPrinterTotalColor(
            (int)$this->createQueryBuilder('p')
                ->andWhere('p.isColorPrinter = 1')
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $summary->setPrinterTonerOk(
            (int)$this->createQueryBuilder('p')
                ->orWhere('p.TonerBlack > 30 AND p.isColorPrinter = 0')
                ->orWhere('p.isColorPrinter = 1 AND p.TonerBlack > 30 AND p.TonerYellow > 30 AND p.TonerMagenta > 30 AND p.TonerCyan > 30')
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $summary->setPrinterTonerWarning(
            (int)$this->createQueryBuilder('p')
                ->orWhere('p.TonerCyan < 30')
                ->andWhere('p.TonerCyan > 10')
                ->orWhere('p.TonerMagenta < 30')
                ->andWhere('p.TonerMagenta > 10')
                ->orWhere('p.TonerYellow < 30')
                ->andWhere('p.TonerYellow > 10')
                ->orWhere('p.TonerBlack < 30')
                ->andWhere('p.TonerBlack > 10')
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $summary->setPrinterTonerDanger(
            (int)$this->createQueryBuilder('p')
                ->orWhere('p.TonerBlack < 10 AND p.isColorPrinter = 0')
                ->orWhere('p.isColorPrinter = 1 AND (p.TonerBlack < 10 OR p.TonerYellow < 10 OR p.TonerMagenta < 10 OR p.TonerCyan < 10)')
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT b.max_pages - b.min_pages AS pages_per_day
                FROM (SELECT MAX(total_pages) as max_pages,
                             MIN(total_pages) as min_pages
                      FROM printer_history
                      WHERE timestamp BETWEEN DATE_SUB(NOW(), INTERVAL :days DAY) AND DATE(NOW())
                      GROUP BY DATE(timestamp)) b;";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'days' => 30
        ]);
        $summary->setTotalAvgPages($stmt->fetchColumn(0));

        return $summary;
    }

    // /**
    //  * @return Printer[] Returns an array of Printer objects
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
    public function findOneBySomeField($value): ?Printer
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
