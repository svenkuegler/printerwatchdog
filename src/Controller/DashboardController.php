<?php

namespace App\Controller;

use App\Repository\PrinterHistoryRepository;
use App\Repository\PrinterRepository;
use App\Service\SnipeITService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard/{view}", name="dashboard", defaults={"view"="dash"})
     *
     * @param PrinterRepository $printerRepository
     * @param $view
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(PrinterRepository $printerRepository, $view)
    {
        return $this->render('dashboard/index.html.twig', [
            'view' => $this->_validateViewParam($view),
            'printerList' => $printerRepository->findAll(),
            'printerSummary' => $printerRepository->getSummary()
        ]);
    }

    /**
     * @Route("/dashboard/{id}/detail", name="dashboard_details")
     *
     * @param Request $request
     * @param PrinterRepository $printerRepository
     * @param PrinterHistoryRepository $printerHistoryRepository
     * @param SnipeITService $snipeITService
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function details(Request $request, PrinterRepository $printerRepository, PrinterHistoryRepository $printerHistoryRepository, SnipeITService $snipeITService)
    {
        $printer = $printerRepository->findOneBy(['id' => $request->get("id")]);
        $printerHistory = $printerHistoryRepository->findAllGroupByDay($printer);
        $printerStatistic = $printerHistoryRepository->get30DaysUsage($printer);
        $snipeItInfo = $snipeITService->getAssetInformationBySerial($printer->getSerialNumber());

        return $this->render('dashboard/detail.html.twig', [
            'printer' => $printer,
            'printerHistory' => $printerHistory,
            'printerStatistic' => $printerStatistic,
            'snipeItUrl' => $this->getParameter('snipeit.url'),
            'snipeItInfo' => $snipeItInfo
        ]);
    }

    /**
     * @param string $view
     * @return mixed|string
     */
    private function _validateViewParam(string $view)
    {
        $availableViews = ['dash', 'card', 'table'];
        return (!in_array($view, $availableViews)) ? $availableViews[0] : $view;
    }
}
