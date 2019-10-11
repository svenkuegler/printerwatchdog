<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/_simulate")
 */
class SimulateController extends AbstractController
{
    /**
     * @Route("/500", name="simulate500")
     */
    public function simulate500()
    {
        $response = new Response($this->renderView("bundles/TwigBundle/Exception/error.html.twig", ['status_code'=>500, 'status_text'=>'Simulated Server Error']), 500);
        return $response;
    }

    /**
     * @Route("/404", name="simulate404")
     */
    public function simulate404()
    {
        $response = new Response($this->renderView("bundles/TwigBundle/Exception/error404.html.twig"), 404);
        return $response;
    }

    /**
     * @Route("/403", name="simulate403")
     */
    public function simulate403()
    {
        $response = new Response($this->renderView("bundles/TwigBundle/Exception/error403.html.twig"), 403);
        return $response;
    }
}
