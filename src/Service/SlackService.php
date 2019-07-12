<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

class SlackService
{

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