<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    /**
     * @Route("/card", name="card")
     */
    public function cardIndexRoute(): Response
    {
        return $this->render("card.html.twig");
    }
        /**
     * @Route("/card/deck", name="card-deck")
     */
    public function cardDeckRoute(): Response
    {
        return $this->render("card.html.twig");
    }
    /**
     * @Route("/card/deck/shuffle", name="card-deck-shuffle")
     */
    public function cardDeckShuffleRoute(): Response
    {
        return $this->render("card.html.twig");
    }
    /**
     * @Route("/card/deck/draw/:number", name="card-deck-draw")
     */
    public function cardDeckDrawRoute(): Response
    {
        return $this->render("card.html.twig");
    }
    /**
     * @Route("/card/deck/deal/:players/:cards", name="card-deck-deal")
     */
    public function cardDeckDealRoute(): Response
    {
        return $this->render("card.html.twig");
    }
    /**
     * @Route("/card/deck2", name="card-deck-joker")
     */
    public function cardDeckJokerRoute(): Response
    {
        return $this->render("card.html.twig");
    }
}
