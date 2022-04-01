<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function indexRoute(): Response
    {
        return $this->render("index.html.twig");
    }

    /**
     * @Route("/report", name="report")
     */
    public function reportRoute(): Response
    {
        return $this->render("report.html.twig");
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutRoute(): Response
    {
        return $this->render("about.html.twig");
    }
}
