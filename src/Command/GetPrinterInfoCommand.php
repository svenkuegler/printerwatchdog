<?php

namespace App\Command;

use App\Entity\Printer;
use App\Repository\PrinterRepository;
use App\Service\SNMPHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;


class GetPrinterInfoCommand extends Command
{
    protected static $defaultName = 'app:get-printer-info';
    private $logger;
    private $printerRepository;
    private $container;

    public function __construct(LoggerInterface $logger, PrinterRepository $printerRepository, ContainerInterface $container)
    {
        $this->logger = $logger;
        $this->printerRepository = $printerRepository;
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('ip', InputArgument::OPTIONAL, 'IP Address: 192.168.1.0')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip = $input->getArgument('ip');
        $em = $this->container->get('doctrine')->getEntityManager();

        $io = new SymfonyStyle($input, $output);
        $snmpHelper = new SNMPHelper($this->logger, $this->container);

        if(!is_null($ip))
        {
            $io->note(sprintf("Trying to get information for %s.", $ip));
            $printer = $this->printerRepository->findOneBy(["Ip" => $ip]);
            $printerInformation = $snmpHelper->getPrinterInfo($ip);

            if($printer) {
                $io->success(sprintf("%s found in DB.", $ip));

                $printer->setLocation($printerInformation->sysLocation);
                $printer->setLastCheck(new \DateTime("now"));
                $printer->setSerialNumber($printerInformation->serialNumber);
                $printer->setIsColorPrinter($printerInformation->isColorPrinter);
                $printer->setTonerBlack($printerInformation->tonerBlack);
                $printer->setType((isset($printerInformation->printerType)) ? $printerInformation->printerType:"");
                $printer->setTotalPages($printerInformation->totalPages);

                $em->persist($printer);
                $em->flush();

            } else {
                $q = new Question(sprintf("Cant find IP %s in DB. Do you want to add? [y/N]", $ip));
                $a = $io->askQuestion($q);
                if($a == "yes" || $a == "y") {
                    $printer = new Printer();
                    $printer->setName($ip);
                    $printer->setIp($ip);
                    $printer->setLocation($printerInformation->sysLocation);
                    $printer->setLastCheck(new \DateTime("now"));
                    $printer->setSerialNumber($printerInformation->serialNumber);
                    $printer->setIsColorPrinter($printerInformation->isColorPrinter);
                    $printer->setTonerBlack($printerInformation->tonerBlack);
                    $printer->setTonerYellow(0);
                    $printer->setTonerCyan(0);
                    $printer->setTonerMagenta(0);
                    $printer->setType($printerInformation->printerType);
                    $printer->setTotalPages($printerInformation->totalPages);

                    $em->persist($printer);
                    $em->flush();
                }
            }
        }
        else
        {
            $io->note("Trying to get all printer information");

            $printerList = $this->printerRepository->findAll();
            foreach ($printerList as $printer) {

                $this->logger->notice(sprintf("Get information for IP %s", $printer->getIp()));
                $printerInformation = $snmpHelper->getPrinterInfo($printer->getIp());

                $printer->setLocation((isset($printerInformation->sysLocation)) ? $printerInformation->sysLocation : "");
                $printer->setSerialNumber((isset($printerInformation->serialNumber)) ? $printerInformation->serialNumber : "");
                $printer->setIsColorPrinter((isset($printerInformation->isColorPrinter)) ? $printerInformation->isColorPrinter : "");
                $printer->setTonerBlack((isset($printerInformation->tonerBlack)) ? $printerInformation->tonerBlack : "");
                $printer->setType((isset($printerInformation->printerType)) ? $printerInformation->printerType : "");
                $printer->setTotalPages((isset($printerInformation->totalPages)) ? $printerInformation->totalPages : "");
                $printer->setLastCheck(new \DateTime("now"));

                $this->logger->notice(sprintf("Save actual information for IP %s", $printer->getIp()));
                $em->persist($printer);
                $em->flush();
            }
        }
        $this->logger->info("Test");
    }
}
