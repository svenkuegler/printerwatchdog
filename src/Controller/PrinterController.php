<?php

namespace App\Controller;

use App\Entity\Printer;
use App\Form\BulkIpType;
use App\Form\PrinterType;
use App\Repository\PrinterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/printer")
 */
class PrinterController extends AbstractController
{
    /**
     * @Route("/", name="printer_index", methods={"GET"})
     */
    public function index(PrinterRepository $printerRepository): Response
    {
        return $this->render('printer/index.html.twig', [
            'printers' => $printerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="printer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $printer = new Printer();
        $form = $this->createForm(PrinterType::class, $printer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($printer);
            $entityManager->flush();

            return $this->redirectToRoute('printer_index');
        }

        return $this->render('printer/new.html.twig', [
            'printer' => $printer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bulkadd", name="printer_bulkadd", methods={"GET","POST"})
     */
    public function bulkadd(Request $request): Response
    {
        $printer = new Printer();
        $form = $this->createForm(BulkIpType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $ipList = explode("\n", $data['ips']);

            foreach ($ipList as $ip)
            {
                $ip = trim($ip);
                // Check IP is Valid
                if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    // fill printer entity
                    $printer = new Printer();
                    $printer->setName($ip);
                    $printer->setIp($ip);
                    $printer->setLocation("");
                    $printer->setLastCheck(new \DateTime("now"));
                    $printer->setSerialNumber("");
                    $printer->setIsColorPrinter(false);
                    $printer->setTonerBlack(0);
                    $printer->setTonerYellow(0);
                    $printer->setTonerCyan(0);
                    $printer->setTonerMagenta(0);
                    $printer->setType("");
                    $printer->setTotalPages(0);

                    // persist
                    $entityManager->persist($printer);
                    $entityManager->flush();

                    unset($printer);
                    $this->addFlash("success", sprintf("%s was added successfully", $ip));
                } else {
                    $this->addFlash("danger", sprintf("%s is not a valid IP", $ip));
                }
            }

            return $this->redirectToRoute('printer_index');
        }

        return $this->render('printer/bulkadd.html.twig', [
            'printer' => $printer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="printer_show", methods={"GET"})
     */
    public function show(Printer $printer): Response
    {
        return $this->render('printer/show.html.twig', [
            'printer' => $printer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="printer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Printer $printer): Response
    {
        $form = $this->createForm(PrinterType::class, $printer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('printer_index', [
                'id' => $printer->getId(),
            ]);
        }

        return $this->render('printer/edit.html.twig', [
            'printer' => $printer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="printer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Printer $printer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$printer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($printer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('printer_index');
    }
}
