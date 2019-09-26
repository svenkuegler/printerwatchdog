<?php

namespace App\Entity;

class PrinterInformation
{
    /**
     * @var string
     */
    private $sysDescr = "";

    /**
     * @var string
     */
    private $sysObjID = "";

    /**
     * @var \DateTime
     */
    private $sysUpTime = null;

    /**
     * @var string
     */
    private $sysContact = "";

    /**
     * @var string
     */
    private $sysName = "";

    /**
     * @var string
     */
    private $sysLocation = "";

    /**
     * @var int
     */
    private $sysServices = 0;

    /**
     * @var string
     */
    private $serialNumber = "";

    /**
     * @var int
     */
    private $totalPages = 0;

    /**
     * @var string
     */
    private $printerType = "";

    /**
     * @var array
     */
    private $suppliesTable = [];

    /**
     * @var string
     */
    private $consoleDisplayBuffer = "";

    /**
     * @var int
     */
    private $tonerBlack = 0;

    /**
     * @var string
     */
    private $tonerBlackDescription = "";

    /**
     * @var int
     */
    private $tonerCyan = 0;

    /**
     * @var string
     */
    private $tonerCyanDescription = "";

    /**
     * @var int
     */
    private $tonerMagenta = 0;

    /**
     * @var string
     */
    private $tonerMagentaDescription = "";

    /**
     * @var int
     */
    private $tonerYellow = 0;

    /**
     * @var string
     */
    private $tonerYellowDescription = "";

    /**
     * @var bool
     */
    private $isColorPrinter = false;

    /**
     * @return string
     */
    public function getSysDescr(): string
    {
        return $this->sysDescr;
    }

    /**
     * @param string $sysDescr
     * @return PrinterInformation
     */
    public function setSysDescr(string $sysDescr): PrinterInformation
    {
        $this->sysDescr = $sysDescr;
        return $this;
    }

    /**
     * @return string
     */
    public function getSysObjID(): string
    {
        return $this->sysObjID;
    }

    /**
     * @param string $sysObjID
     * @return PrinterInformation
     */
    public function setSysObjID(string $sysObjID): PrinterInformation
    {
        $this->sysObjID = $sysObjID;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSysUpTime(): \DateTime
    {
        return $this->sysUpTime;
    }

    /**
     * @param int $sysUpTime
     * @return PrinterInformation
     * @throws \Exception
     */
    public function setSysUpTime(int $sysUpTime): PrinterInformation
    {
        $this->sysUpTime = new \DateTime('now'); //$sysUpTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getSysContact(): string
    {
        return $this->sysContact;
    }

    /**
     * @param string $sysContact
     * @return PrinterInformation
     */
    public function setSysContact(string $sysContact): PrinterInformation
    {
        $this->sysContact = $sysContact;
        return $this;
    }

    /**
     * @return string
     */
    public function getSysName(): string
    {
        return $this->sysName;
    }

    /**
     * @param string $sysName
     * @return PrinterInformation
     */
    public function setSysName(string $sysName): PrinterInformation
    {
        $this->sysName = $sysName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSysLocation(): string
    {
        return $this->sysLocation;
    }

    /**
     * @param string $sysLocation
     * @return PrinterInformation
     */
    public function setSysLocation(string $sysLocation): PrinterInformation
    {
        $this->sysLocation = $sysLocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    /**
     * @param string $serialNumber
     * @return PrinterInformation
     */
    public function setSerialNumber(string $serialNumber): PrinterInformation
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     * @return PrinterInformation
     */
    public function setTotalPages(int $totalPages): PrinterInformation
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrinterType(): string
    {
        return $this->printerType;
    }

    /**
     * @param string $printerType
     * @return PrinterInformation
     */
    public function setPrinterType(string $printerType): PrinterInformation
    {
        $this->printerType = $printerType;
        return $this;
    }

    /**
     * @return array
     */
    public function getSuppliesTable(): array
    {
        return $this->suppliesTable;
    }

    /**
     * @param array $suppliesTable
     * @return PrinterInformation
     */
    public function setSuppliesTable(array $suppliesTable): PrinterInformation
    {
        $this->suppliesTable = $suppliesTable;
        return $this;
    }

    /**
     * @return int
     */
    public function getSysServices(): int
    {
        return $this->sysServices;
    }

    /**
     * @param int $sysServices
     * @return PrinterInformation
     */
    public function setSysServices(int $sysServices): PrinterInformation
    {
        $this->sysServices = $sysServices;
        return $this;
    }

    /**
     * @return int
     */
    public function getTonerBlack(): int
    {
        return $this->tonerBlack;
    }

    /**
     * @param int $tonerBlack
     * @return PrinterInformation
     */
    public function setTonerBlack(int $tonerBlack): PrinterInformation
    {
        $this->tonerBlack = $tonerBlack;
        return $this;
    }

    /**
     * @return int
     */
    public function getTonerCyan(): int
    {
        return $this->tonerCyan;
    }

    /**
     * @param int $tonerCyan
     * @return PrinterInformation
     */
    public function setTonerCyan(int $tonerCyan): PrinterInformation
    {
        $this->tonerCyan = $tonerCyan;
        return $this;
    }

    /**
     * @return int
     */
    public function getTonerMagenta(): int
    {
        return $this->tonerMagenta;
    }

    /**
     * @param int $tonerMagenta
     * @return PrinterInformation
     */
    public function setTonerMagenta(int $tonerMagenta): PrinterInformation
    {
        $this->tonerMagenta = $tonerMagenta;
        return $this;
    }

    /**
     * @return int
     */
    public function getTonerYellow(): int
    {
        return $this->tonerYellow;
    }

    /**
     * @param int $tonerYellow
     * @return PrinterInformation
     */
    public function setTonerYellow(int $tonerYellow): PrinterInformation
    {
        $this->tonerYellow = $tonerYellow;
        return $this;
    }

    /**
     * @return bool
     */
    public function isColorPrinter(): bool
    {
        return $this->isColorPrinter;
    }

    /**
     * @param bool $isColorPrinter
     * @return PrinterInformation
     */
    public function setIsColorPrinter(bool $isColorPrinter): PrinterInformation
    {
        $this->isColorPrinter = $isColorPrinter;
        return $this;
    }

    /**
     * @return string
     */
    public function getTonerBlackDescription(): string
    {
        return $this->tonerBlackDescription;
    }

    /**
     * @param string $tonerBlackDescription
     * @return PrinterInformation
     */
    public function setTonerBlackDescription(string $tonerBlackDescription): PrinterInformation
    {
        $this->tonerBlackDescription = $tonerBlackDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getTonerCyanDescription(): string
    {
        return $this->tonerCyanDescription;
    }

    /**
     * @param string $tonerCyanDescription
     * @return PrinterInformation
     */
    public function setTonerCyanDescription(string $tonerCyanDescription): PrinterInformation
    {
        $this->tonerCyanDescription = $tonerCyanDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getTonerMagentaDescription(): string
    {
        return $this->tonerMagentaDescription;
    }

    /**
     * @param string $tonerMagentaDescription
     * @return PrinterInformation
     */
    public function setTonerMagentaDescription(string $tonerMagentaDescription): PrinterInformation
    {
        $this->tonerMagentaDescription = $tonerMagentaDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getTonerYellowDescription(): string
    {
        return $this->tonerYellowDescription;
    }

    /**
     * @param string $tonerYellowDescription
     * @return PrinterInformation
     */
    public function setTonerYellowDescription(string $tonerYellowDescription): PrinterInformation
    {
        $this->tonerYellowDescription = $tonerYellowDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsoleDisplayBuffer(): string
    {
        return $this->consoleDisplayBuffer;
    }

    /**
     * @param string $consoleDisplayBuffer
     * @return PrinterInformation
     */
    public function setConsoleDisplayBuffer(string $consoleDisplayBuffer): PrinterInformation
    {
        $this->consoleDisplayBuffer = $consoleDisplayBuffer;
        return $this;
    }

}