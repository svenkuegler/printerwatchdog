<?php

namespace App\Service;

use App\Entity\Printer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

class SlackService
{

    const WarningColor = '#ffc107';
    const DangerColor = '#dc3545';

    private $_logger;
    private $_container;
    private $_message = "";
    private $_attachments = [];

    /**
     * SlackService constructor.
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        if (!function_exists("curl_init")) {
            throw new \Exception("Can't send a slack message, because curl is not available!");
        }
        $this->_logger = $logger;
        $this->_container = $container;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->_attachments;
    }

    /**
     * @param array $attachments
     * @return SlackService
     */
    public function setAttachments(array $attachments): SlackService
    {
        $this->_attachments = $attachments;
        return $this;
    }

    /**
     * @param Printer $printer
     * @return $this
     */
    public function setWarningAttachment(Printer $printer) {
        $rC = $this->_container->get('router')->getContext();
        $this->_attachments = [
            [
                'fallback' => 'Toner level is warning',
                'title'  => 'Toner level is warning',
                'title_link' => sprintf('%s/dashboard/%s/detail', $rC->getBaseUrl() . (($rC->getHttpPort() != 80) ? ':' . $rC->getHttpPort() : ''), $printer->getId()),
                'color'    => SlackService::WarningColor,
                'fields'   => [
                    [
                        'title' => 'Location',
                        'value' => $printer->getLocation(),
                        'short' => true
                    ],
                    [
                        'title' => 'IP',
                        'value' => '<http://' . $printer->getIp() . '|' . $printer->getIp() . '>',
                        'short' => true
                    ],
                    [
                        'title' => 'Toner',
                        'value' => "Black: " . $printer->getTonerBlack() . "%" . (($printer->getisColorPrinter())? "\nYellow: " . $printer->getTonerYellow() . "%\nCyan: " . $printer->getTonerCyan() . "%\nMagenta: " . $printer->getTonerMagenta() . "%%": ""),
                        'short' => true
                    ]
                ]
            ]
        ];

        return $this;
    }

    /**
     * @param Printer $printer
     * @return $this
     */
    public function setDangerAttachment(Printer $printer) {
        $rC = $this->_container->get('router')->getContext();
        $this->_attachments = [
            [
                'fallback' => 'Toner level is danger',
                'title'  => 'Toner level is danger',
                'title_link' => sprintf('%s/dashboard/%s/detail', $rC->getBaseUrl() . (($rC->getHttpPort() != 80) ? ':' . $rC->getHttpPort() : ''), $printer->getId()),
                'color'    => SlackService::DangerColor,
                'fields'   => [
                    [
                        'title' => 'Location',
                        'value' => $printer->getLocation(),
                        'short' => true
                    ],
                    [
                        'title' => 'IP',
                        'value' => '<http://' . $printer->getIp() . '|' . $printer->getIp() . '>',
                        'short' => true
                    ],
                    [
                        'title' => 'Toner',
                        'value' => "Black: " . $printer->getTonerBlack() . "%" . (($printer->getisColorPrinter())? "\nYellow: " . $printer->getTonerYellow() . "%\nCyan: " . $printer->getTonerCyan() . "%\nMagenta: " . $printer->getTonerMagenta() . "%%": ""),
                        'short' => true
                    ]
                ]
            ]
        ];

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): SlackService
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * @return array
     */
    private function _getPayload(): array
    {
        return [
            'payload' => json_encode([
                'text' => $this->getMessage(),
                "icon_emoji" => ":printer:",
                "username" => "PrinterWatchdog",
                "attachments" => $this->getAttachments()
            ])
        ];
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->_message;
    }

    /**
     *
     */
    public function send()
    {
        // Use curl to send your message
        $c = curl_init($this->_container->getParameter("slack.webhook"));
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $this->_getPayload());
        $this->_logger->info(curl_exec($c));
        curl_close($c);
    }
}