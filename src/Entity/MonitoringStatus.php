<?php

namespace App\Entity;

class MonitoringStatus {

    /**
     * @var string
     */
    public $status = 'UNKNOWN';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var string
     */
    public $performanceData = '';

    /**
     * @var /DateTime
     */
    public $timestamp;

    /**
     * MonitoringStatus constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setTimestamp(new \DateTime());
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return MonitoringStatus
     */
    public function setStatus(string $status): MonitoringStatus
    {
        if(in_array($status, $this->validState())) {
            $this->status = $status;
        } else {
            $this->status = 'UNKNOWN';
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return MonitoringStatus
     */
    public function setMessage(string $message): MonitoringStatus
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     * @return MonitoringStatus
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getPerformanceData(): string
    {
        return $this->performanceData;
    }

    /**
     * @param string $performanceData
     * @return MonitoringStatus
     */
    public function setPerformanceData(string $performanceData): MonitoringStatus
    {
        $this->performanceData = $performanceData;
        return $this;
    }

    /**
     * valid service states
     * @return array
     */
    private function validState() {
        return [
            'OK',
            'WARNING',
            'CRITICAL',
            'UNKNOWN'
        ];
    }
}