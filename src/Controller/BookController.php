<?php

namespace App\Controller;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
use App\Cards\CardGraphic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book-index")
     */
    public function indexRoute(SessionInterface $session): Response
    {
        return $this->render("book/index.html.twig");
    }
    /**
     * @Route("/book/list", name="book-list")
     */
    public function bookListRoute(SessionInterface $session): Response
    {
        return $this->render("book/booklist.html.twig");
    }
        /**
     * @Route("/book/add", name="book-add")
     */
    public function addBookRoute(SessionInterface $session): Response
    {
        return $this->render("book/bookform.html.twig");
    }
}
