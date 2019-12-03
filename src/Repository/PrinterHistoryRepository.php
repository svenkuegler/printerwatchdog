<?php

namespace App\Repository;

use App\Entity\Printer;
use App\Entity\PrinterHistory;
use App\Entity\PrinterHistoryStatisticData;
use App\Entity\PrinterHistoryStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PrinterHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrinterHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrinterHistory[]    findAll()
 * @method PrinterHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrinterHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrinterHistory::class);
    }

    /**
     * @param Printer $printer
     * @return PrinterHistory[]
     */
    public function findAllGroupByDay(Printer $printer)
    {
        return $this->createQueryBuilder('ph')
            ->select('MAX(ph.TotalPages) AS TotalPages, MIN(ph.TonerBlack) AS TonerBlack, MIN(ph.TonerYellow) AS TonerYellow ,MIN(ph.TonerCyan) AS TonerCyan ,MIN(ph.TonerMagenta) AS TonerMagenta ,ph.Timestamp ,DATE_FORMAT(ph.Timestamp, \'%Y-%m-%d \') AS dateSub')
            ->andWhere('ph.Printer = :printer')
            ->setParameter('printer', $printer)
            ->groupBy("dateSub")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Printer $printer
     * @return PrinterHistoryStatistics
     * @throws \Doctrine\DBAL\DBALException
     */
    public function get30DaysUsage(Printer $printer)
    {
        $printerStatistics = new PrinterHistoryStatistics();
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT b.id, b.printer_id,
                       b.max_pages - b.min_pages AS pages_per_day,
                       b.max_black - b.min_black AS black_per_day,
                       b.max_yellow - b.min_yellow AS yellow_per_day,
                       b.max_cyan - b.min_cyan AS cyan_per_day,
                       b.max_magenta - b.min_magenta AS magenta_per_day,
                       b.formated_date
                FROM (SELECT id,
                             printer_id,
                             MAX(total_pages) as max_pages,
                             MIN(total_pages) as min_pages,
                             MAX(toner_black) as max_black,
                             MIN(toner_black) as min_black,
                             MAX(toner_yellow) as max_yellow,
                             MIN(toner_yellow) as min_yellow,
                             MAX(toner_magenta) as max_magenta,
                             MIN(toner_magenta) as min_magenta,
                             MAX(toner_cyan) as max_cyan,
                             MIN(toner_cyan) as min_cyan,
                             DATE(timestamp)  as formated_date
                      FROM printer_history
                      WHERE printer_id = :printerId
                        AND timestamp BETWEEN DATE_SUB(NOW(), INTERVAL :days DAY) AND DATE(NOW())
                      GROUP BY DATE(timestamp)) b;";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'printerId' => $printer->getId(),
            'days' => 30
        ]);

        foreach ($stmt->fetchAll() as $stats)
        {
            $ps = new PrinterHistoryStatisticData();
            $ps->setPrinterId($stats['printer_id'])
                ->setFormatedDate($stats['formated_date'])
                ->setPagesPerDay($stats['pages_per_day'])
                ->setBlackPerDay($stats['black_per_day'])
                ->setYellowPerDay($stats['yellow_per_day'])
                ->setMagentaPerDay($stats['magenta_per_day'])
                ->setCyanPerDay($stats['cyan_per_day'])
                ;

            $printerStatistics->addStatistic($ps);
            unset($ps);
        }

        return $printerStatistics;
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
