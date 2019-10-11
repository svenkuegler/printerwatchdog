<?php

namespace App\Service;

use App\Entity\Report;
use App\Repository\PrinterRepository;

class ReportHelperService {

    /**
     * @var string
     */
    private $report = "";

    /**
     * @var PrinterRepository
     */
    private $printerRepository;

    public function __construct(PrinterRepository $printerRepository)
    {
        $this->printerRepository = $printerRepository;
    }

    /**
     * @return Report
     * @throws \Exception
     */
    public function getReportData()
    {
        switch ($this->getReport()) {
            case 'asset':
                $r = new ReportAsset($this->printerRepository);
                $data = $r->get();
                break;

            case 'toner':
                $r = new ReportToner($this->printerRepository);
                $data = $r->get();
                break;

            default:
                $data = new Report();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getReport(): string
    {
        return $this->report;
    }

    /**
     * @param string $report
     * @return ReportHelperService
     */
    public function setReport(string $report): ReportHelperService
    {
        $this->report = $report;
        return $this;
    }


}