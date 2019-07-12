<?php

namespace App\Controller;

use App\Repository\PrinterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'controller_name' => 'DashboardController',
            'printerList' => $printerRepository->findAll()
        ]);
    }

    /**
     * @param string $view
     * @return mixed|string
     */
    private function _validateViewParam(string $view)
    {
        $availableViews = ['card', 'table', "row"];
        return (!in_array($view, $availableViews)) ? $availableViews[0] : $view;
    }
}
