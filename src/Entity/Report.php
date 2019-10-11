<?php

namespace App\Entity;

class Report
{
    /**
     * @var string
     */
    private $report = "";

    /**
     * @var string
     */
    private $displayName = "";

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var array
     */
    private $dataHeader = [];

    /**
     * @var array
     */
    private $data = [];

    /* -------------------------------------------------------- */

    /**
     * @return string
     */
    public function getReport(): string
    {
        return $this->report;
    }

    /**
     * @param string $report
     * @return Report
     */
    public function setReport(string $report): Report
    {
        $this->report = $report;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return Report
     */
    public function setDisplayName(string $displayName): Report
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Report
     */
    public function setData(array $data): Report
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return Report
     */
    public function setCreated(\DateTime $created): Report
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return array
     */
    public function getDataHeader(): array
    {
        return $this->dataHeader;
    }

    /**
     * @param array $dataHeader
     * @return Report
     */
    public function setDataHeader(array $dataHeader): Report
    {
        $this->dataHeader = $dataHeader;
        return $this;
    }


}