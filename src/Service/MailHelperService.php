<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

class MailHelperService {

    /**
     * @var \Swift_Message
     */
    private $message;

    /**
     * @var \Swift_Mailer
     */
    private $_mailer;

    /**
     * @var Environment
     */
    private $_twig;

    /**
     * @var ContainerInterface
     */
    private $_container;


    public function __construct(\Swift_Mailer $mailer, Environment $twig, ContainerInterface $container)
    {
        $this->_mailer = $mailer;
        $this->_twig = $twig;
        $this->_container = $container;
    }

    public function send() {
        $this->_mailer->send($this->getMessage()->addFrom($this->_container->getParameter("mailer.from")));
    }


    private function getMessage(){
        if(!$this->message instanceof \Swift_Message) {
            $this->message = new \Swift_Message();
        }
        return $this->message;
    }

    /**
     * @param string $subject
     * @return MailHelperService
     */
    public function setSubject(string $subject): MailHelperService
    {
        $this->getMessage()->setSubject($subject);
        return $this;
    }

    /**
     * @param string $messageTemplate
     * @param array $messageParams
     * @return MailHelperService
     */
    public function setMessageTemplate(string $messageTemplate, array $messageParams): MailHelperService
    {
        try{
            $this->getMessage()->setBody($this->_twig->render(
                $messageTemplate,
                $messageParams
            ), 'text/html');

        } catch (\Exception $e) {
            $this->getMessage()->setBody(sprintf("<h1>Ooops there was an error.</h1><br><br>%s", $e->getMessage()), 'text/html');
        }

        return $this;
    }

    /**
     * @param array $recipients
     * @return MailHelperService
     */
    public function setRecipients(array $recipients): MailHelperService
    {
        foreach ($recipients as $recipient) {
            $this->getMessage()->addTo($recipient);
        }
        return $this;
    }

}