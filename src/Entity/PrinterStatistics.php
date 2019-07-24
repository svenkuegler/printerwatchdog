<?php

namespace App\Entity;

class PrinterStatistics
{
    /**
     * @var Printer
     */
    private $printer;

    /**
     * @var int
     */
    private $avg30daysPages = 0;

    /**
     * @var float
     */
    private $avg30daysBlackUsage = 0;

    /**
     * @var float
     */
    private $avg30daysYellowUsage = 0;

    /**
     * @var float
     */
    private $avg30daysMagentaUsage = 0;

    /**
     * @var float
     */
    private $avg30daysCyanUsage = 0;

    /**
     * @var PrinterStatistic[]
     */
    private $statistics;

    /**
     * @return Printer
     */
    public function getPrinter(): Printer
    {
        return $this->printer;
    }

    /**
     * @param Printer $printer
     * @return PrinterStatistics
     */
    public function setPrinter(Printer $printer): PrinterStatistics
    {
        $this->printer = $printer;
        return $this;
    }

    /**
     * @return int
     */
    public function getAvg30daysPages(): int
    {
        return $this->avg30daysPages;
    }

    /**
     * @param int $avg30daysPages
     * @return PrinterStatistics
     */
    public function setAvg30daysPages(int $avg30daysPages): PrinterStatistics
    {
        $this->avg30daysPages = $avg30daysPages;
        return $this;
    }

    /**
     * @return float
     */
    public function getAvg30daysBlackUsage(): float
    {
        return $this->avg30daysBlackUsage;
    }

    /**
     * @param float $avg30daysBlackUsage
     * @return PrinterStatistics
     */
    public function setAvg30daysBlackUsage(float $avg30daysBlackUsage): PrinterStatistics
    {
        $this->avg30daysBlackUsage = $avg30daysBlackUsage;
        return $this;
    }

    /**
     * @return float
     */
    public function getAvg30daysYellowUsage(): float
    {
        return $this->avg30daysYellowUsage;
    }

    /**
     * @param float $avg30daysYellowUsage
     * @return PrinterStatistics
     */
    public function setAvg30daysYellowUsage(float $avg30daysYellowUsage): PrinterStatistics
    {
        $this->avg30daysYellowUsage = $avg30daysYellowUsage;
        return $this;
    }

    /**
     * @return float
     */
    public function getAvg30daysMagentaUsage(): float
    {
        return $this->avg30daysMagentaUsage;
    }

    /**
     * @param float $avg30daysMagentaUsage
     * @return PrinterStatistics
     */
    public function setAvg30daysMagentaUsage(float $avg30daysMagentaUsage): PrinterStatistics
    {
        $this->avg30daysMagentaUsage = $avg30daysMagentaUsage;
        return $this;
    }

    /**
     * @return float
     */
    public function getAvg30daysCyanUsage(): float
    {
        return $this->avg30daysCyanUsage;
    }

    /**
     * @param float $avg30daysCyanUsage
     * @return PrinterStatistics
     */
    public function setAvg30daysCyanUsage(float $avg30daysCyanUsage): PrinterStatistics
    {
        $this->avg30daysCyanUsage = $avg30daysCyanUsage;
        return $this;
    }

    /**
     * @return PrinterStatistic[]
     */
    public function getStatistics(): array
    {
        return $this->statistics;
    }

    /**
     * @param PrinterStatistic[] $statistics
     * @return PrinterStatistics
     */
    public function setStatistics(array $statistics): PrinterStatistics
    {
        $this->statistics = $statistics;
        return $this;
    }

    /**
     * @param PrinterStatistic $statistic
     * @return PrinterStatistics
     */
    public function addStatistic(PrinterStatistic $statistic): PrinterStatistics
    {
        $this->statistics[] = $statistic;
        $this->calculateAvg();
        return $this;
    }

    /**
     * Calculate Avg by statistics
     */
    private function calculateAvg() : void
    {
        $tmpDays = [];
        if(count($this->statistics)>0) {
            foreach ($this->getStatistics() as $stat) {
                $tmpDays[]['pages'] = $stat->getPagesPerDay();
                $tmpDays[]['black'] = $stat->getBlackPerDay();
                $tmpDays[]['yellow'] = $stat->getYellowPerDay();
                $tmpDays[]['magenta'] = $stat->getMagentaPerDay();
                $tmpDays[]['cyan'] = $stat->getCyanPerDay();
            }

            $this->setAvg30daysPages(array_sum(array_column($tmpDays, 'pages'))/count($tmpDays));
            $this->setAvg30daysBlackUsage(array_sum(array_column($tmpDays, 'black'))/count($tmpDays));
            $this->setAvg30daysYellowUsage(array_sum(array_column($tmpDays, 'yellow'))/count($tmpDays));
            $this->setAvg30daysMagentaUsage(array_sum(array_column($tmpDays, 'magenta'))/count($tmpDays));
            $this->setAvg30daysCyanUsage(array_sum(array_column($tmpDays, 'cyan'))/count($tmpDays));
        }
    }
}