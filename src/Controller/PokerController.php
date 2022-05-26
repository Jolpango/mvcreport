<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PokerController extends AbstractController
{
    /**
     * @Route("/proj", name="proj")
     */
    public function pokerIndexRoute(SessionInterface $session): Response
    {
        return $this->render("poker/index.html.twig");
    }
    /**
     *  Route("/proj/register", name="proj-register")
     */
    public function pokerRegisterRoute(SessionInterface $session): Response
    {
        return $this->render("poker/register.html.twig");
    }
}
