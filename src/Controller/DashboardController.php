<?php

namespace App\Controller;

use App\Repository\PrinterRepository;
use App\Service\SnipeITService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    /**
     * @Route("/dashboard/{view}", name="dashboard", defaults={"view"="card"})
     */
    public function index(PrinterRepository $printerRepository, $view)
    {
        return $this->render('dashboard/index.html.twig', [
            'view' => $this->_validateViewParam($view),
            'printerList' => $printerRepository->findAll()
        ]);
    }

    /**
     * @Route("/dashboard/{id}/detail", name="dashboard_details")
     */
    public function details(Request $request, PrinterRepository $printerRepository, SnipeITService $snipeITService)
    {
        $printer = $printerRepository->findOneBy(['id' => $request->get("id")]);
        $snipeItInfo = $snipeITService->getAssetInformationBySerial($printer->getSerialNumber());

        return $this->render('dashboard/detail.html.twig', [
            'printer' => $printer,
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
        $availableViews = ['card', 'table'];
        return (!in_array($view, $availableViews)) ? $availableViews[0] : $view;
    }
}
