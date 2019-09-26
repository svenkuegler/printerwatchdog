<?php

namespace App\Repository;

use App\Entity\Printer;
use App\Entity\PrinterSummary;
use App\Service\ContainerParametersHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @method Printer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Printer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Printer[]    findAll()
 * @method Printer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrinterRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    private $lvlDanger = 0;

    /**
     * @var int
     */
    private $lvlWarning = 0;

    /**
     * PrinterRepository constructor.
     * @param RegistryInterface $registry
     * @param ContainerParametersHelper $containerParametersHelper
     */
    public function __construct(RegistryInterface $registry, ContainerParametersHelper $containerParametersHelper)
    {
        $config = Yaml::parseFile( $containerParametersHelper->getApplicationRootDir()  . "/config/notification.yaml");
        $this->lvlWarning = $config['web']['tonerlevel']['warning'];
        $this->lvlDanger = $config['web']['tonerlevel']['danger'];

        parent::__construct($registry, Printer::class);
    }

    /**
     * @return Printer[]
     * @param array $filter
     */
    public function findAllWithFilter(array $filter)
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($filter as $f) {
            $qb = $this->_filterFieldMapper($qb, $f);
        }

        return $qb->getQuery()->getResult();
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
                ->orWhere('p.TonerBlack > :lvlWarning AND p.isColorPrinter = 0')
                ->orWhere('p.isColorPrinter = 1 AND p.TonerBlack > :lvlWarning AND p.TonerYellow > :lvlWarning AND p.TonerMagenta > :lvlWarning AND p.TonerCyan > :lvlWarning')
                ->select('COUNT(p.id) as r')
                ->setParameter('lvlWarning', $this->lvlWarning)
                ->getQuery()->getOneOrNullResult()['r']
        );

        $summary->setPrinterTonerWarning(
            (int)$this->createQueryBuilder('p')
                ->orWhere('p.TonerCyan < :lvlWarning')
                ->andWhere('p.TonerCyan > :lvlDanger')
                ->orWhere('p.TonerMagenta < :lvlWarning')
                ->andWhere('p.TonerMagenta > :lvlDanger')
                ->orWhere('p.TonerYellow < :lvlWarning')
                ->andWhere('p.TonerYellow > :lvlDanger')
                ->orWhere('p.TonerBlack < :lvlWarning')
                ->andWhere('p.TonerBlack > :lvlDanger')
                ->setParameter('lvlDanger', $this->lvlDanger)
                ->setParameter('lvlWarning', $this->lvlWarning)
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $summary->setPrinterTonerDanger(
            (int)$this->createQueryBuilder('p')
                ->orWhere('p.TonerBlack < :lvlDanger AND p.isColorPrinter = 0')
                ->orWhere('p.isColorPrinter = 1 AND (p.TonerBlack < :lvlDanger OR p.TonerYellow < :lvlDanger OR p.TonerMagenta < :lvlDanger OR p.TonerCyan < :lvlDanger)')
                ->setParameter('lvlDanger', $this->lvlDanger)
                ->select('COUNT(p.id) as r')
                ->getQuery()->getOneOrNullResult()['r']
        );

        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT ROUND(AVG(c.pages_per_day) / :days) as avg_pages from  (
                SELECT b.max_pages - b.min_pages AS pages_per_day
                FROM (SELECT MAX(total_pages) as max_pages,
                             MIN(total_pages) as min_pages
                      FROM printer_history
                      WHERE timestamp BETWEEN DATE_SUB(NOW(), INTERVAL :days DAY) AND DATE(NOW())
                      GROUP BY DATE(timestamp)) b) c;";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'days' => 30
        ]);
        $summary->setTotalAvgPages(intval($stmt->fetchColumn(0)));

        return $summary;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $filter
     * @return QueryBuilder
     */
    private function _filterFieldMapper(QueryBuilder $queryBuilder, string $filter) {

        switch($filter) {
            case 'fUnreachable':
                $queryBuilder->andWhere('p.unreachableCount > 0');
                break;

            case 'fDanger':
                $queryBuilder->orWhere('p.TonerBlack < :lvlDanger AND p.isColorPrinter = 0')
                    ->orWhere('p.isColorPrinter = 1 AND (p.TonerBlack < :lvlDanger OR p.TonerYellow < :lvlDanger OR p.TonerMagenta < :lvlDanger OR p.TonerCyan < :lvlDanger)')
                    ->setParameter('lvlDanger', $this->lvlDanger);
                break;

            case 'fWarning':
                $queryBuilder->orWhere('p.TonerCyan < :lvlWarning')
                    ->andWhere('p.TonerCyan > :lvlDanger')
                    ->orWhere('p.TonerMagenta < :lvlWarning')
                    ->andWhere('p.TonerMagenta > :lvlDanger')
                    ->orWhere('p.TonerYellow < :lvlWarning')
                    ->andWhere('p.TonerYellow > :lvlDanger')
                    ->orWhere('p.TonerBlack < :lvlWarning')
                    ->andWhere('p.TonerBlack > :lvlDanger')
                    ->setParameter('lvlDanger', $this->lvlDanger)
                    ->setParameter('lvlWarning', $this->lvlWarning);
                break;

            case 'fOkay':
                $queryBuilder->orWhere('p.TonerBlack > :lvlWarning AND p.isColorPrinter = 0')
                    ->orWhere('p.isColorPrinter = 1 AND p.TonerBlack > :lvlWarning AND p.TonerYellow > :lvlWarning AND p.TonerMagenta > :lvlWarning AND p.TonerCyan > :lvlWarning')
                    ->setParameter('lvlWarning', $this->lvlWarning);
                break;
        }

        return $queryBuilder;
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
