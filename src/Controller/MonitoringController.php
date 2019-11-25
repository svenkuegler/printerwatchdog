<?php

namespace App\Controller;

use App\Entity\MonitoringStatus;
use App\Repository\PrinterRepository;
use App\Service\ContainerParametersHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/monitoring")
 */
class MonitoringController extends AbstractController
{
    /**
     * @var array
     */
    private $notificationConfig = [];

    /**
     * MonitoringController constructor.
     * @param ContainerParametersHelper $containerParametersHelper
     */
    public function __construct(ContainerParametersHelper $containerParametersHelper)
    {
        $this->notificationConfig = Yaml::parseFile( $containerParametersHelper->getApplicationRootDir()  . "/config/notification.yaml");
    }

    /**
     * @Route("/", name="monitoring_index")
     *
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(TranslatorInterface $translator) : Response
    {
        $state = sprintf("<strong>%s</strong>", $translator->trans("Disabled"));
        if($this->notificationConfig['monitoring']['enabled']) {
            $state =
                sprintf("<strong>%s</strong><br><small>Warning Level: %s, Danger Level: %s</small>",
                    $translator->trans("Enabled"),
                    (($this->notificationConfig['monitoring']['tonerlevel']['warning']==0) ? 'none' : $this->notificationConfig['monitoring']['tonerlevel']['warning'] . '%'),
                    (($this->notificationConfig['monitoring']['tonerlevel']['danger']==0) ? 'none' : $this->notificationConfig['monitoring']['tonerlevel']['danger'] . '%')
            );
        }
        return $this->render("monitoring/index.html.twig", [
            'monitoringState' => $state
        ]);
    }

    /**
     * @Route("/status", name="monitoring_status")
     *
     * @param PrinterRepository $printerRepository
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function status(PrinterRepository $printerRepository, Request $request) : Response
    {
        if(!in_array($request->getClientIp(), array_values($this->getParameter('monitoring.allowed_ips')))) {
            return new Response('ip not allowed', Response::HTTP_FORBIDDEN);
        }

        $state = new MonitoringStatus();
        if($this->notificationConfig['monitoring']['enabled']) {

            $summary = $printerRepository
                ->setLvlDanger($this->notificationConfig['monitoring']['tonerlevel']['danger'])
                ->setLvlWarning($this->notificationConfig['monitoring']['tonerlevel']['warning'])
                ->getSummary();

            $state->setPerformanceData(sprintf('%s;%s;%s;%s;%s',
                $summary->getLastCheck(),
                ($summary->getPrinterTotalColor()+$summary->getPrinterTotalSw()),
                $summary->getPrinterTonerOk(),
                $summary->getPrinterTonerWarning(),
                $summary->getPrinterTonerDanger()
            ));

            if($summary->getPrinterTonerDanger() > 0) {
                $state
                    ->setStatus('CRITICAL')
                    ->setMessage(sprintf('%s printer with toner level Danger', $summary->getPrinterTonerDanger()));

            } elseif ($summary->getPrinterTonerWarning() > 0) {
                $state
                    ->setStatus('WARNING')
                    ->setMessage(sprintf('%s printer with toner level Warning', $summary->getPrinterTonerWarning()));

            } else {
                $state->setStatus('OK')->setMessage('everything looks fine');
            }
        } else {
            $state->setStatus('UNKNOWN')->setMessage("monitoring is disabled");
        }

        return $this->json($state);
    }
}
