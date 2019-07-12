<?php

namespace App\Command;

use App\Repository\PrinterRepository;
use App\Service\ContainerParametersHelper;
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

    private $_logger;
    private $_container;
    private $_printerRepository;
    private $_helper;

    public function __construct(LoggerInterface $logger, ContainerInterface $container, PrinterRepository $printerRepository, ContainerParametersHelper $helper)
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_container = $container;
        $this->_printerRepository = $printerRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Send Notification to given targets.')
            //->addOption('slack', null, InputOption::VALUE_NONE, 'Use Slack Notification')
            //->addOption('email', null, InputOption::VALUE_NONE, 'Use E-Mail Notification')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $config = Yaml::parseFile( $this->_helper->getApplicationRootDir()  . "/config/notification.yaml");
        $slack = new SlackService($this->_logger, $this->_container);


        $printers = $this->_printerRepository->findAll();
        foreach ($printers as $printer) {

            // EMail
            if($printer->getTonerBlack() <= $config['email']['tonerlevel']['danger']) {
                // TODO: send notification email
            } elseif ($printer->getTonerBlack() <= $config['email']['tonerlevel']['warning']) {
                // TODO: send notification email
            }

            // Slack
            if($printer->getTonerBlack() <= $config['email']['tonerlevel']['danger']) {
                $slack->setMessage(sprintf('Printer %s toner level is danger level!', $printer->getName()))->send();
            } elseif ($printer->getTonerBlack() <= $config['email']['tonerlevel']['warning']) {
                $slack->setMessage(sprintf('Printer %s toner level is warning!', $printer->getName()))->send();
            }

        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
