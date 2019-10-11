<?php

namespace App\Service;

use App\Entity\Report;
use App\Repository\PrinterRepository;

class ReportToner extends ReportAbstract
{
    private $printerRepository;

    /**
     * ReportToner constructor.
     * @param PrinterRepository $printerRepository
     */
    public function __construct(PrinterRepository $printerRepository)
    {
        $this->setReport('toner');
        $this->printerRepository = $printerRepository;
    }

    /**
     * @return Report
     * @throws \Exception
     */
    public function get() : Report
    {
        $d = [];
        $h = [
            'Total',
            'Printer Type',
            'Toner Black',
            'Toner Cyan',
            'Toner Magenta',
            'Toner Yellow',
        ];
        $result = $this->printerRepository->getTonerReport();

        foreach ($result as $entry)
        {
            $d[] = [
                $entry["total"],
                $entry["Type"],
                $entry["toner_black"],
                $entry["toner_cyan"],
                $entry["toner_magenta"],
                $entry["toner_yellow"],
            ];
        }

        $data = new Report();
        $data
            ->setCreated(new \DateTime('now'))
            ->setReport($this->getReport())
            ->setDisplayName($this->getReport())
            ->setData($d)
            ->setDataHeader($h);

        return $data;
    }
}