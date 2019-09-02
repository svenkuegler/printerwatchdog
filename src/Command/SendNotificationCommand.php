<?php

namespace App\Command;

use App\Entity\Printer;
use App\Repository\PrinterRepository;
use App\Repository\UserRepository;
use App\Service\ContainerParametersHelper;
use App\Service\MailHelperService;
use App\Service\SlackService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class SendNotificationCommand extends Command
{
    protected static $defaultName = 'app:send-notification';

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * @var PrinterRepository
     */
    private $_printerRepository;

    /**
     * @var UserRepository
     */
    private $_userRepository;

    /**
     * @var ContainerParametersHelper
     */
    private $_helper;

    /**
     * @var MailHelperService
     */
    private $_mailHelper;

    /**
     * @var SlackService
     */
    private $_slackHelper;

    /**
     * SendNotificationCommand constructor.
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     * @param PrinterRepository $printerRepository
     * @param UserRepository $userRepository
     * @param ContainerParametersHelper $helper
     * @param MailHelperService $mailHelper
     * @param SlackService $slack
     */
    public function __construct(LoggerInterface $logger, ContainerInterface $container, PrinterRepository $printerRepository, UserRepository $userRepository, ContainerParametersHelper $helper, MailHelperService $mailHelper, SlackService $slack)
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_container = $container;
        $this->_printerRepository = $printerRepository;
        $this->_userRepository = $userRepository;
        $this->_mailHelper = $mailHelper;
        $this->_slackHelper = $slack;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Send Notification to given targets.')
            ->addOption('slack', null, InputOption::VALUE_NONE, 'Use Slack Notification')
            ->addOption('email', null, InputOption::VALUE_NONE, 'Use E-Mail Notification')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $config = Yaml::parseFile( $this->_helper->getApplicationRootDir()  . "/config/notification.yaml");
        $this->_logger->info(sprintf("Starting notification with %s %s", ($input->getOption('email'))? "option email," : "no email option," , ($input->getOption('slack'))?'option slack.':'no slack option.'));
        $unreachableCount = $this->_container->getParameter('printer.inactive.unreachable_count');
        $notifyUnreachable = $this->_container->getParameter('printer.inactive.notification_enabled');

        $printers = $this->_printerRepository->findAll();
        foreach ($printers as $printer) {

            // skip notification for unreachable printer
            if($printer->getUnreachableCount() > $unreachableCount) {
                if($notifyUnreachable == false)
                    continue;
            }

            // EMail
            if($config['email']['enabled'] && $input->getOption('email')) {
                $this->_notifyWithEMail($printer, $config['email']['tonerlevel']['warning'], $config['email']['tonerlevel']['danger']);
            }

            // Slack
            if($config['slack']['enabled'] && $input->getOption('slack')) {
                $this->_notifyWithSlack($printer, $config['slack']['tonerlevel']['warning'], $config['slack']['tonerlevel']['danger']);
            }
        }

        $this->_logger->info('Successfully notified!');
    }

    /**
     * @param Printer $printer
     * @param int $warningLevel
     * @param int $dangerLevel
     */
    private function _notifyWithEMail(Printer $printer, int $warningLevel, int $dangerLevel) {
        if($printer->getTonerBlack() <= $dangerLevel || ($printer->getisColorPrinter() && ($printer->getTonerMagenta()<=$dangerLevel || $printer->getTonerCyan()<=$dangerLevel || $printer->getTonerYellow()<=$dangerLevel))) {
            $this->_mailHelper
                ->setSubject("Toner Level Danger for " . $printer->getSerialNumber())
                ->setRecipients($this->_userRepository->getAllEMailAddresses())
                ->setMessageTemplate("mails/danger_notification.html.twig", ['printer' => $printer])
                ->send();
        } elseif($printer->getTonerBlack() <= $warningLevel || ($printer->getisColorPrinter() && ($printer->getTonerMagenta()<=$warningLevel || $printer->getTonerCyan()<=$warningLevel || $printer->getTonerYellow()<=$warningLevel))) {
            $this->_mailHelper
                ->setSubject("Toner Level Warning for " . $printer->getSerialNumber())
                ->setRecipients($this->_userRepository->getAllEMailAddresses())
                ->setMessageTemplate("mails/warning_notification.html.twig", ['printer' => $printer])
                ->send();
        }
    }

    /**
     * @param Printer $printer
     * @param int $warningLevel
     * @param int $dangerLevel
     */
    private function _notifyWithSlack(Printer $printer, int $warningLevel, int $dangerLevel) {
        if($printer->getTonerBlack() <= $dangerLevel || ($printer->getisColorPrinter() && ($printer->getTonerMagenta()<=$dangerLevel || $printer->getTonerCyan()<=$dangerLevel || $printer->getTonerYellow()<=$dangerLevel))) {
            $this->_slackHelper
                ->setMessage(sprintf('Toner Level is danger for %s!', $printer->getSerialNumber()))
                ->setDangerAttachment($printer)
                ->send();
        } elseif($printer->getTonerBlack() <= $warningLevel || ($printer->getisColorPrinter() && ($printer->getTonerMagenta()<=$warningLevel || $printer->getTonerCyan()<=$warningLevel || $printer->getTonerYellow()<=$warningLevel))) {
            $this->_slackHelper
                ->setMessage(sprintf('Toner Level is warning for %s!', $printer->getSerialNumber()))
                ->setWarningAttachment($printer)
                ->send();
        }
    }
}
