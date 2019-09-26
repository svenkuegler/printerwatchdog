<?php

namespace App\Entity;

class PrinterHistoryStatisticData
{
    /**
     * @var int
     */
    private $printer_id = 0;

    /**
     * @var
     */
    private $formated_date;

    /**
     * @var int
     */
    private $pages_per_day = 0;

    /**
     * @var int
     */
    private $black_per_day = 0;

    /**
     * @var int
     */
    private $yellow_per_day = 0;

    /**
     * @var int
     */
    private $cyan_per_day = 0;

    /**
     * @var int
     */
    private $magenta_per_day = 0;

    /**
     * @return int
     */
    public function getPrinterId(): int
    {
        return $this->printer_id;
    }

    /**
     * @param int $printer_id
     * @return PrinterHistoryStatisticData
     */
    public function setPrinterId(int $printer_id): PrinterHistoryStatisticData
    {
        $this->printer_id = $printer_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormatedDate()
    {
        return $this->formated_date;
    }

    /**
     * @param mixed $formated_date
     * @return PrinterHistoryStatisticData
     */
    public function setFormatedDate($formated_date)
    {
        $this->formated_date = $formated_date;
        return $this;
    }

    /**
     * @return int
     */
    public function getPagesPerDay(): int
    {
        return $this->pages_per_day;
    }

    /**
     * @param int $pages_per_day
     * @return PrinterHistoryStatisticData
     */
    public function setPagesPerDay(int $pages_per_day): PrinterHistoryStatisticData
    {
        $this->pages_per_day = $pages_per_day;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlackPerDay(): int
    {
        return $this->black_per_day;
    }

    /**
     * @param int $black_per_day
     * @return PrinterHistoryStatisticData
     */
    public function setBlackPerDay(int $black_per_day): PrinterHistoryStatisticData
    {
        $this->black_per_day = $black_per_day;
        return $this;
    }

    /**
     * @return int
     */
    public function getYellowPerDay(): int
    {
        return $this->yellow_per_day;
    }

    /**
     * @param int $yellow_per_day
     * @return PrinterHistoryStatisticData
     */
    public function setYellowPerDay(?int $yellow_per_day): PrinterHistoryStatisticData
    {
        $this->yellow_per_day = intval($yellow_per_day);
        return $this;
    }

    /**
     * @return int
     */
    public function getCyanPerDay(): int
    {
        return $this->cyan_per_day;
    }

    /**
     * @param int $cyan_per_day
     * @return PrinterHistoryStatisticData
     */
    public function setCyanPerDay(?int $cyan_per_day): PrinterHistoryStatisticData
    {
        $this->cyan_per_day = intval($cyan_per_day);
        return $this;
    }

    /**
     * @return int
     */
    public function getMagentaPerDay(): int
    {
        return $this->magenta_per_day;
    }

    /**
     * @param int $magenta_per_day
     * @return PrinterHistoryStatisticData
     */
    public function setMagentaPerDay(?int $magenta_per_day): PrinterHistoryStatisticData
    {
        $this->magenta_per_day = intval($magenta_per_day);
        return $this;
    }



}