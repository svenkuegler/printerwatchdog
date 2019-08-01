<?php

namespace App\Service;

use App\Entity\Printer;
use phpDocumentor\Reflection\Types\This;
use App\Entity\PrinterInformation;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class SNMPHelper
{

    /**
     * @var \SNMP::class
     */
    private $SNMPConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SNMPHelper constructor.
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        if (!function_exists("snmpget")) {
            throw new \Exception('There is no SNMP-PHP-Module active!');
        }

        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * @param string $ip
     * @param int $version
     * @param string $community
     *
     * @return bool
     */
    private function _connect(string $ip, int $version = \SNMP::VERSION_1, $community = "public")
    {
        if(!$this->isReachable($ip)) {
            return false;
        }

        try {
            $this->SNMPConnection = new \SNMP($version, $ip, $community);
            $this->SNMPConnection->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
            $this->SNMPConnection->oid_increasing_check = false;

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (\SNMPException $se) {
            $this->logger->error($se->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param string $oid
     * @return mixed|string
     */
    private function _get(string $oid)
    {
        $ret = "";
        try {
            $ret = $this->SNMPConnection->get($oid);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        } catch (\SNMPException $se) {
            $this->logger->warning($se->getMessage());
        }

        return $ret;
    }

    /**
     * @param string $oid
     * @return int|string
     */
    private function _getValue(string $oid)
    {
        return $this->_getConvertToType($this->_get($oid));
    }

    /**
     * @param $value
     * @return int|string
     */
    private function _getConvertToType($value)
    {
        if (preg_match("/STRING: \"(.*)\"/", $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/STRING: (.*)/", $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/INTEGER: (.*)/", $value, $matches)) {
            $value = intval($matches[1]);
        } elseif (preg_match("/Counter32: (.*)/", $value, $matches)) {
            $value = intval($matches[1]);
        } elseif (preg_match("/Timeticks: \((.*)\) (.*)/", $value, $matches)) {
            $value = intval($matches[1]);
        } elseif (preg_match("/OID: (.*)/", $value, $matches)) {
            $value = $matches[1];
        }

        return $value;
    }

    /**
     * @param $startOID
     * @return array
     */
    private function _getTable($startOID) : array
    {
        $numCols = 9;
        $cols = [];
        for ($i=2;$i<=$numCols;$i++) {
            try{
                $arr[$i] = $this->SNMPConnection->walk(sprintf("%s.1.%s", $startOID, $i));
            } catch (\Exception $e) {
                $arr[$i] = null;
            }
            $cols[$i] = $arr[$i];
        }

        $table = [];
        foreach ($cols as $col=>$value) {
            $row=1;
            foreach ($value as $v)
            {
                $table[$row][$this->_colNames($col)] = $this->_getConvertToType($v);
                $row++;
            }
        }

        return $table;
    }

    /**
     * @param $idx
     * @return string
     */
    private function _colNames($idx) : string
    {
        $arr = [
            1 => "prtMarkerSuppliesIndex",
            2 => "prtMarkerSuppliesMarkerIndex",
            3 => "prtMarkerSuppliesColorantIndex",
            4 => "prtMarkerSuppliesClass",
            5 => "prtMarkerSuppliesType",
            6 => "prtMarkerSuppliesDescription",
            7 => "prtMarkerSuppliesSupplyUnit",
            8 => "prtMarkerSuppliesMaxCapacity",
            9 => "prtMarkerSuppliesLevel",
        ];
        return $arr[$idx];
    }

    private function _close() : void
    {
        $this->SNMPConnection->close();
    }

    /**
     * Ugly, quick and dirty cleanup
     *
     * @param PrinterInformation $printerInformation
     */
    private function _getTonerInfo(PrinterInformation $printerInformation) : void
    {
        $tonerOnly = [];
        foreach ($printerInformation->getSuppliesTable() as $row)
        {
            if($row['prtMarkerSuppliesType'] == 3) {
                $tonerOnly[] = (object)$row;
            }
        }
        $printerInformation->setSuppliesTable($tonerOnly);

        if(count($printerInformation->getSuppliesTable())==1) {
            $printerInformation
                ->setTonerBlack(round($printerInformation->getSuppliesTable()[0]->prtMarkerSuppliesLevel * 100 / $printerInformation->getSuppliesTable()[0]->prtMarkerSuppliesMaxCapacity))
                ->setTonerBlackDescription($printerInformation->getSuppliesTable()[0]->prtMarkerSuppliesDescription);
        } else {
            $printerInformation->setIsColorPrinter(true);

            // TODO: Find a better way
            // No Idea how to find out what color
            // in the most cases the followed code works
            // Please drop me a message if you find a better way
            foreach ($printerInformation->getSuppliesTable() as $tbl)
            {
                if(strpos(strtolower($tbl->prtMarkerSuppliesDescription), "black") !== false) {
                    $printerInformation
                        ->setTonerBlack(round($tbl->prtMarkerSuppliesLevel * 100 / $tbl->prtMarkerSuppliesMaxCapacity))
                        ->setTonerBlackDescription($tbl->prtMarkerSuppliesDescription);
                }

                if(strpos(strtolower($tbl->prtMarkerSuppliesDescription), "yellow") !== false) {
                    $printerInformation
                        ->setTonerYellow(round($tbl->prtMarkerSuppliesLevel * 100 / $tbl->prtMarkerSuppliesMaxCapacity))
                        ->setTonerYellowDescription($tbl->prtMarkerSuppliesDescription);
                }

                if(strpos(strtolower($tbl->prtMarkerSuppliesDescription), "cyan") !== false) {
                    $printerInformation
                        ->setTonerCyan(round($tbl->prtMarkerSuppliesLevel * 100 / $tbl->prtMarkerSuppliesMaxCapacity))
                        ->setTonerCyanDescription($tbl->prtMarkerSuppliesDescription);
                }

                if(strpos(strtolower($tbl->prtMarkerSuppliesDescription), "magenta") !== false) {
                    $printerInformation
                        ->setTonerMagenta(round($tbl->prtMarkerSuppliesLevel * 100 / $tbl->prtMarkerSuppliesMaxCapacity))
                        ->setTonerMagentaDescription($tbl->prtMarkerSuppliesDescription);
                }
            }
        }
    }

    /**
     * @param String $ip
     * @return PrinterInformation|null
     */
    public function getPrinterInfo(String $ip)
    {
        if(!$this->_connect($ip)) {
            $this->_close();
            return null;
        }

        $results = new PrinterInformation();

        if(!$this->isDevicePrinter($ip)) {
            $this->logger->info(sprintf("%s not seems to be a printer.", $ip));
            return $results;
        }

        // get default values
        $defaultOIDs = $this->container->getParameter("snmp.default");
        foreach ($defaultOIDs as $name => $options) {
            if($options['type'] == "table") {
                call_user_func([$results, "set" . ucfirst($name)], $this->_getTable($options['oid']));
            } else {
                call_user_func([$results, "set" . ucfirst($name)], $this->_getValue($options['oid']));
            }
        }

        // get and override enterprise specific values
        if($this->container->hasParameter(sprintf("snmp.enterprise.oid%s", $results->getSysObjID()))) {
            $specificOIDs = $this->container->getParameter(sprintf("snmp.enterprise.oid%s", $results->getSysObjID()));
            foreach ($specificOIDs as $name => $options) {
                if($options['type'] == "table") {
                    call_user_func([$results, "set" . ucfirst($name)], $this->_getTable($options['oid']));
                } else {
                    call_user_func([$results, "set" . ucfirst($name)], $this->_getValue($options['oid']));
                }
            }
        }

        // get Toner Infos
        $this->_getTonerInfo($results);

        // Set Description as Type if empty
        if(strlen($results->getPrinterType())<3) {
            $results->setPrinterType($results->getSysDescr());
        }

        $this->_close();

        return $results;
    }

    /**
     * @param String $ip
     * @param string $oid
     * @return int|string|null
     */
    public function getSingleValue(String $ip, string $oid)
    {
        if(!$this->_connect($ip)) {
            $this->_close();
            return null;
        }

        $result = $this->_getValue($oid);

        $this->_close();

        return $result;
    }

    /**
     * @param $ip
     * @return bool
     */
    public function isDevicePrinter($ip) {
        $result = $this->_getValue(".1.3.6.1.2.1.25.3.2.1.2.1");
        return ($result == ".1.3.6.1.2.1.25.3.1.5") ? true : false;
    }

    /**
     * @param $ip
     * @return bool
     */
    public function isReachable($ip) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec(sprintf('ping -n 1 %s', escapeshellarg($ip)), $res, $rval);
        } else {
            exec(sprintf('ping -c 1 %s', escapeshellarg($ip)), $res, $rval);
        }
        return $rval === 0;
    }
}