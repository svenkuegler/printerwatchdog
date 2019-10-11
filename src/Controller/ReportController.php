<?php

namespace App\Controller;

use App\Entity\Report;
use App\Repository\PrinterRepository;
use App\Service\ReportAsset;
use App\Service\ReportHelperService;
use App\Service\ReportToner;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/generate/{type}/{report}", name="generate_report")
     *
     * @param Request $request
     * @param PrinterRepository $printerRepository
     *
     * @return Response|null
     */
    public function generateReport(Request $request, PrinterRepository $printerRepository)
    {
        $report = $request->get('report');
        $type = $request->get('type');
        $response = null;

        switch ($report) {
            case 'asset':
                $r = new ReportAsset($printerRepository);
                $reportData = $r->get();
                break;

            case 'toner':
                $r = new ReportToner($printerRepository);
                $reportData = $r->get();
                break;

            default:

        }


        $filename = sprintf("export_%s_%s.%s",$report, date("Y_m_d_His"), $type);

        switch($type)
        {
            /* ------------------------------------------------------- */
            case 'html':
                $response = $this->render('report/report_view.html.twig', [
                    'report_data'=> $reportData,
                    'creation_date' => $reportData->getCreated()
                ]);
                break;


            /* ------------------------------------------------------- */
            case 'csv':
                $response = $this->render('report/export.csv.twig', ['data' => $reportData->getData(), 'separator' => ';']);

                $response->headers->set('Content-Type', 'text/csv');
                $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
                break;


            /* ------------------------------------------------------- */
            case 'pdf':
                $pdfOptions = new Options();
                $pdfOptions
                    ->set('defaultFont', 'Helvetica')
                    ->set('enable_remote', true);

                $domPdf = new Dompdf($pdfOptions);

                $html = $this->renderView('report/report_view.html.twig', [
                    'report_data'=> $reportData,
                    'creation_date' => $reportData->getCreated()
                ]);

                $domPdf->loadHtml($html);

                $domPdf->setPaper('A4', 'portrait');
                $domPdf->render();

                $response = new Response($domPdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename='.$filename
                ]);
                break;
        }

        return $response;
    }


}
