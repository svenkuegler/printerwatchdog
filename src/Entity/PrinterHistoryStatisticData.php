<?php

namespace App\Entity;

class PrinterHistoryStatisticData
{
    /**
     * @var int
     */
    private $printerId = 0;

    /**
     * @var
     */
    private $formatedDate;

    /**
     * @var int
     */
    private $pagesPerDay = 0;

    /**
     * @var int
     */
    private $blackPerDay = 0;

    /**
     * @var int
     */
    private $yellowPerDay = 0;

    /**
     * @var int
     */
    private $cyanPerDay = 0;

    /**
     * @var int
     */
    private $magentaPerDay = 0;

    /**
     * @return int
     */
    public function getPrinterId(): int
    {
        return $this->printerId;
    }

    /**
     * @param int $printer_id
     * @return PrinterHistoryStatisticData
     */
    public function setPrinterId(int $printer_id): PrinterHistoryStatisticData
    {
        $this->printerId = $printer_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormatedDate()
    {
        return $this->formatedDate;
    }

    /**
     * @param mixed $formatedDate
     * @return PrinterHistoryStatisticData
     */
    public function setFormatedDate($formatedDate)
    {
        $this->formatedDate = $formatedDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getPagesPerDay(): int
    {
        return $this->pagesPerDay;
    }

    /**
     * @param int $pagesPerDay
     * @return PrinterHistoryStatisticData
     */
    public function setPagesPerDay(int $pagesPerDay): PrinterHistoryStatisticData
    {
        $this->pagesPerDay = $pagesPerDay;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlackPerDay(): int
    {
        return $this->blackPerDay;
    }

    /**
     * @param int $blackPerDay
     * @return PrinterHistoryStatisticData
     */
    public function setBlackPerDay(int $blackPerDay): PrinterHistoryStatisticData
    {
        $this->blackPerDay = $blackPerDay;
        return $this;
    }

    /**
     * @return int
     */
    public function getYellowPerDay(): int
    {
        return $this->yellowPerDay;
    }

    /**
     * @param int $yellowPerDay
     * @return PrinterHistoryStatisticData
     */
    public function setYellowPerDay(?int $yellowPerDay): PrinterHistoryStatisticData
    {
        $this->yellowPerDay = intval($yellowPerDay);
        return $this;
    }

    /**
     * @return int
     */
    public function getCyanPerDay(): int
    {
        return $this->cyanPerDay;
    }

    /**
     * @param int $cyanPerDay
     * @return PrinterHistoryStatisticData
     */
    public function setCyanPerDay(?int $cyanPerDay): PrinterHistoryStatisticData
    {
        $this->cyanPerDay = intval($cyanPerDay);
        return $this;
    }

    /**
     * @return int
     */
    public function getMagentaPerDay(): int
    {
        return $this->magentaPerDay;
    }

    /**
     * @param int $magentaPerDay
     * @return PrinterHistoryStatisticData
     */
    public function setMagentaPerDay(?int $magentaPerDay): PrinterHistoryStatisticData
    {
        $this->magentaPerDay = intval($magentaPerDay);
        return $this;
    }



}