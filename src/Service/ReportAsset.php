<?php

namespace App\Service;

use App\Entity\Report;
use App\Repository\PrinterRepository;
use Doctrine\ORM\Query;

class ReportAsset extends ReportAbstract
{
    private $printerRepository;

    /**
     * ReportAsset constructor.
     * @param PrinterRepository $printerRepository
     */
    public function __construct(PrinterRepository $printerRepository)
    {
        $this->setReport('asset');
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
            'ID',
            'IP',
            'Serial Number',
            'Location',
        ];
        $result = $this->printerRepository->findAll();
        foreach ($result as $entry)
        {
            $d[] = [
                $entry->getId(),
                $entry->getIp(),
                $entry->getSerialNumber(),
                $entry->getLocation(),
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