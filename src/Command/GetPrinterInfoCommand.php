<?php

namespace App\Command;

use App\Entity\Printer;
use App\Entity\PrinterHistory;
use App\Entity\PrinterInformation;
use App\Repository\PrinterRepository;
use App\Repository\UserRepository;
use App\Service\MailHelperService;
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PrinterRepository
     */
    private $printerRepository;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var MailHelperService
     */
    private $mailHelperService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * GetPrinterInfoCommand constructor.
     * @param LoggerInterface $logger
     * @param PrinterRepository $printerRepository
     * @param ContainerInterface $container
     * @param MailHelperService $mailHelperService
     * @param UserRepository $userRepository
     */
    public function __construct(LoggerInterface $logger, PrinterRepository $printerRepository, ContainerInterface $container, MailHelperService $mailHelperService, UserRepository $userRepository)
    {
        $this->logger = $logger;
        $this->printerRepository = $printerRepository;
        $this->container = $container;
        $this->mailHelperService = $mailHelperService;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('ip', InputArgument::OPTIONAL, 'IP Address: 192.168.1.0');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip = $input->getArgument('ip');
        $em = $this->container->get('doctrine')->getManager();

        $io = new SymfonyStyle($input, $output);
        $snmpHelper = new SNMPHelper($this->logger, $this->container);
        $historyData = new PrinterHistory();

        if (!is_null($ip)) {
            $io->note(sprintf("Trying to get information for %s.", $ip));
            $printer = $this->printerRepository->findOneBy(["Ip" => $ip]);
            $pInfo = $snmpHelper->getPrinterInfo($ip);

            if (is_null($pInfo)) {
                $this->logger->error(sprintf("Could not get information for IP %s", $printer->getIp()));
                if ($printer) {
                    // increase counter
                    $printer->setUnreachableCount($printer->getUnreachableCount() + 1);
                    $em->persist($printer);
                    $em->flush();
                }
            } else {
                $historyData->setTotalPages($pInfo->getTotalPages())
                    ->setTonerBlack($pInfo->getTonerBlack())
                    ->setTonerCyan($pInfo->getTonerCyan())
                    ->setTonerMagenta($pInfo->getTonerMagenta())
                    ->setTonerYellow($pInfo->getTonerYellow())
                    ->setTimestamp(new \DateTime("now"));

                if ($printer) {
                    $io->note(sprintf("%s exists in DB, prepare to update ....", $ip));
                    $printer = $this->mapPrinterInformation($printer, $pInfo);

                    $historyData->setPrinter($printer);

                    $em->persist($printer);
                    $em->persist($historyData);
                    $em->flush();

                    $io->success(sprintf("%s ´s infos updated in DB.", $ip));

                } else {
                    $q = new Question(sprintf("Cant find IP %s in DB. Do you want to add? [y/N]", $ip));
                    $a = $io->askQuestion($q);
                    if ($a == "yes" || $a == "y") {
                        $prn = new Printer();
                        $prn->setName($ip)
                            ->setIp($ip)
                            ->setLocation($pInfo->getSysLocation())
                            ->setLastCheck(new \DateTime("now"))
                            ->setSerialNumber($pInfo->getSerialNumber())
                            ->setIsColorPrinter($pInfo->isColorPrinter())
                            ->setTonerBlack($pInfo->getTonerBlack())
                            ->setTonerBlackDescription($pInfo->getTonerBlackDescription())
                            ->setTonerYellow($pInfo->getTonerYellow())
                            ->setTonerYellowDescription($pInfo->getTonerYellowDescription())
                            ->setTonerCyan($pInfo->getTonerCyan())
                            ->setTonerCyanDescription($pInfo->getTonerCyanDescription())
                            ->setTonerMagenta($pInfo->getTonerMagenta())
                            ->setTonerMagentaDescription($pInfo->getTonerMagentaDescription())
                            ->setType($pInfo->getPrinterType())
                            ->setTotalPages($pInfo->getTotalPages())
                            ->setConsoleDisplay($pInfo->getConsoleDisplayBuffer())
                            ->setUnreachableCount(0);
                        $historyData->setPrinter($prn);

                        $em->persist($prn);
                        $em->persist($historyData);
                        $em->flush();
                        $io->success(sprintf("%s ´s infos written.", $ip));
                    }
                }
            }

        } else {
            $io->note("Trying to get all printer information");

            $printerList = $this->printerRepository->findAll();
            foreach ($printerList as $printer) {

                $this->logger->info(sprintf("Get information for IP %s", $printer->getIp()));
                $pInfo = $snmpHelper->getPrinterInfo($printer->getIp());
                if (is_null($pInfo)) {

                    // printer seams to be unreachable, increase counter
                    $printer->setUnreachableCount($printer->getUnreachableCount() + 1);
                    $this->logger->error(sprintf("Could not get information for IP %s (%s times)", $printer->getIp(), $printer->getUnreachableCount()));

                    // printer reached inactive count, send notification
                    if($this->container->getParameter('printer.inactive.unreachable_count') == $printer->getUnreachableCount()) {
                        $this->logger->error("printer reached inactive count, send notification");
                        $this->mailHelperService
                            ->setMessageTemplate("mails/inactive_notification.html.twig", ['printer' => $printer])
                            ->setRecipients($this->userRepository->getAllEMailAddresses())
                            ->setSubject(sprintf("Printer %s is now marked as inactive", $printer->getSerialNumber()))
                            ->send();
                    }

                    $em->persist($printer);
                    $em->flush();
                } else {

                    $this->logger->notice(sprintf("Save actual information for IP %s", $printer->getIp()));
                    $em->persist($this->mapPrinterInformation($printer, $pInfo));

                    $historyData = new PrinterHistory();
                    $historyData->setTotalPages($pInfo->getTotalPages())
                        ->setTonerBlack($pInfo->getTonerBlack())
                        ->setTonerCyan($pInfo->getTonerCyan())
                        ->setTonerMagenta($pInfo->getTonerMagenta())
                        ->setTonerYellow($pInfo->getTonerYellow())
                        ->setTimestamp(new \DateTime("now"))
                        ->setPrinter($printer);

                    $em->persist($historyData);
                    $em->flush();
                }

            } // End Foreach
        }
        $this->logger->info("Get Printer Information finished!");
    }

    /**
     * @param Printer $printer
     * @param PrinterInformation $printerInformation
     * @return Printer
     * @throws \Exception
     */
    private function mapPrinterInformation(Printer $printer, PrinterInformation $printerInformation)
    {
        $printer
            ->setLocation($printerInformation->getSysLocation())
            ->setLastCheck(new \DateTime("now"))
            ->setSerialNumber($printerInformation->getSerialNumber())
            ->setIsColorPrinter($printerInformation->isColorPrinter())
            ->setTonerBlack($printerInformation->getTonerBlack())
            ->setTonerBlackDescription($printerInformation->getTonerBlackDescription())
            ->setTonerYellow($printerInformation->getTonerYellow())
            ->setTonerYellowDescription($printerInformation->getTonerYellowDescription())
            ->setTonerCyan($printerInformation->getTonerCyan())
            ->setTonerCyanDescription($printerInformation->getTonerCyanDescription())
            ->setTonerMagenta($printerInformation->getTonerMagenta())
            ->setTonerMagentaDescription($printerInformation->getTonerMagentaDescription())
            ->setType($printerInformation->getPrinterType())
            ->setTotalPages($printerInformation->getTotalPages())
            ->setConsoleDisplay($printerInformation->getConsoleDisplayBuffer())
            ->setUnreachableCount(0);

        return $printer;
    }
}
