<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ReportController
 * @package App\Controller
 *
 * @Route("/report")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/", name="report")
     */
    public function index()
    {
        return $this->render('report/index.html.twig', [
        ]);
    }

    /**
     * @Route("/generateview/{report}", name="generate_report_view")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function generateReportView(Request $request)
    {
        $r = $request->get('report');

        return $this->render('report/report_view.html.twig', [
            'report_title'=> $r,
            'creation_date' => new \DateTime('now')
        ]);
    }

    /**
     * @Route("/exportpdf/{report}", name="generate_pdf_export")
     * @param Request $request
     */
    public function generatePdfReport(Request $request)
    {

    }

    /**
     * @Route("/exportcsv/{report}", name="generate_csv_export")
     * @param Request $request
     */
    public function generateCsvReport(Request $request)
    {

    }
}
