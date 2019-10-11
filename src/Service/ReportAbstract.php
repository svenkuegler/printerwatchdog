<?php

namespace App\Service;

use App\Entity\Report;

abstract class ReportAbstract
{
    private $report = "";

    abstract public function get() : Report;

    /**
     * @return string
     */
    public function getReport(): string
    {
        return $this->report;
    }

    /**
     * @param string $report
     * @return ReportAbstract
     */
    public function setReport(string $report): ReportAbstract
    {
        $this->report = $report;
        return $this;
    }


}