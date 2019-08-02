<?php

namespace App\Entity;

/**
 * Class PrinterSummary
 * @package App\Entity
 */
class PrinterSummary
{
    /**
     * @var ?string
     */
    private $lastCheck;

    /**
     * @var int
     */
    private $printerTotalSw = 0;

    /**
     * @var int
     */
    private $printerTotalColor = 0;

    /**
     * @var int
     */
    private $recentlyUnreachable = 0;

    /**
     * @var float
     */
    private $totalAvgPages = 0;

    /**
     * @var int
     */
    private $recentErrors = 0;

    /**
     * @var int
     */
    private $printerTonerOk = 0;

    /**
     * @var int
     */
    private $printerTonerWarning = 0;

    /**
     * @var int
     */
    private $printerTonerDanger = 0;

    /**
     * @return string
     */
    public function getLastCheck(): string
    {
        return $this->lastCheck;
    }

    /**
     * @param string|null $lastCheck
     * @return PrinterSummary
     */
    public function setLastCheck(?string $lastCheck): PrinterSummary
    {
        $this->lastCheck = $lastCheck;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrinterTotalSw(): int
    {
        return $this->printerTotalSw;
    }

    /**
     * @param int $printerTotalSw
     * @return PrinterSummary
     */
    public function setPrinterTotalSw(int $printerTotalSw): PrinterSummary
    {
        $this->printerTotalSw = $printerTotalSw;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrinterTotalColor(): int
    {
        return $this->printerTotalColor;
    }

    /**
     * @param int $printerTotalColor
     * @return PrinterSummary
     */
    public function setPrinterTotalColor(int $printerTotalColor): PrinterSummary
    {
        $this->printerTotalColor = $printerTotalColor;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecentlyUnreachable(): int
    {
        return $this->recentlyUnreachable;
    }

    /**
     * @param int $recentlyUnreachable
     * @return PrinterSummary
     */
    public function setRecentlyUnreachable(int $recentlyUnreachable): PrinterSummary
    {
        $this->recentlyUnreachable = $recentlyUnreachable;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalAvgPages(): float
    {
        return $this->totalAvgPages;
    }

    /**
     * @param float $totalAvgPages
     * @return PrinterSummary
     */
    public function setTotalAvgPages(float $totalAvgPages): PrinterSummary
    {
        $this->totalAvgPages = $totalAvgPages;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecentErrors(): int
    {
        return $this->recentErrors;
    }

    /**
     * @param int $recentErrors
     * @return PrinterSummary
     */
    public function setRecentErrors(int $recentErrors): PrinterSummary
    {
        $this->recentErrors = $recentErrors;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrinterTonerOk(): int
    {
        return $this->printerTonerOk;
    }

    /**
     * @param int $printerTonerOk
     * @return PrinterSummary
     */
    public function setPrinterTonerOk(int $printerTonerOk): PrinterSummary
    {
        $this->printerTonerOk = $printerTonerOk;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrinterTonerWarning(): int
    {
        return $this->printerTonerWarning;
    }

    /**
     * @param int $printerTonerWarning
     * @return PrinterSummary
     */
    public function setPrinterTonerWarning(int $printerTonerWarning): PrinterSummary
    {
        $this->printerTonerWarning = $printerTonerWarning;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrinterTonerDanger(): int
    {
        return $this->printerTonerDanger;
    }

    /**
     * @param int $printerTonerDanger
     * @return PrinterSummary
     */
    public function setPrinterTonerDanger(int $printerTonerDanger): PrinterSummary
    {
        $this->printerTonerDanger = $printerTonerDanger;
        return $this;
    }


}