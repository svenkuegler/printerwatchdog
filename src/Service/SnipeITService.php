<?php

namespace App\Service;

use App\Entity\Asset;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SnipeITService
{
    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * @var bool
     */
    private $_isEnabled;

    /**
     * @var mixed
     */
    private $_apiUrl;

    /**
     * @var mixed
     */
    private $_apiKey;

    /**
     * @var string
     */
    private $_result;

    /**
     * SnipeITService constructor.
     *
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        $this->_logger = $logger;
        $this->_container = $container;
        $this->_apiUrl = $container->getParameter("snipeit.url");
        $this->_apiKey = $container->getParameter("snipeit.apikey");

        $this->_isEnabled = (is_null($this->_apiKey))?false:true;
    }

    /**
     * @param string $serialNumber
     * @return Asset|null
     */
    public function getAssetInformationBySerial(string $serialNumber)
    {
        if(!$this->_isEnabled) {
            return null;
        }

        if($this->sendRequest('hardware/byserial/' . $serialNumber)) {
            $assetInfos = json_decode($this->_result);

            if(!$assetInfos || isset($assetInfos->status)) {
                $this->_logger->error("Could not get Asset Information. " . print_r($assetInfos, true));
                $this->_container->get('session')->getFlashBag()->add("danger", sprintf("Could not get Information for SerialNumber %s from SnipeIT.", $serialNumber));
                return null;
            } elseif (intval($assetInfos->total) == 0) {
                $this->_container->get('session')->getFlashBag()->add("danger", sprintf("Could not find Asset for SerialNumber %s at SnipeIT.", $serialNumber));
                return null;
            } elseif (intval($assetInfos->total) != 1) {
                $this->_container->get('session')->getFlashBag()->add("danger", sprintf("Found more than one Asset with SerialNumber %s at SnipeIT.", $serialNumber));
                return null;
            } else {
                $asset = new Asset();
                $asset->setAssetId(intval($assetInfos->rows[0]->id))
                      ->setAssetName($assetInfos->rows[0]->name)
                      ->setAssetTag($assetInfos->rows[0]->asset_tag);
                return $asset;
            }
        } else {
            $this->_container->get('session')->getFlashBag()->add("danger", "Could not get Information from SnipeIT.");
            return null;
        }

    }

    /**
     * @param string $requestUrl
     * @return bool
     */
    private function sendRequest(string $requestUrl)
    {
        $c = @curl_init($this->_apiUrl . "api/v1/" . $requestUrl);
        curl_setopt($c, CURLOPT_HTTPHEADER, ['accept: application/json', 'Authorization: Bearer ' . $this->_apiKey]);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_POST, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $this->_result = curl_exec($c);
        $httpReturnCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        return ($httpReturnCode == 200) ? true : false;
    }
}