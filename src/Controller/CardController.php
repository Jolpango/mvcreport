<?php

namespace App\Controller;

use App\Cards\TwigDeck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardController extends AbstractController
{
    private $deck;
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
    public function cardDeckRoute(SessionInterface $session): Response
    {
        $this->deck = new TwigDeck($session->get("cards") ?? []);
        $session->set("cards", $this->deck->toArray());
        return $this->render("displaycards.html.twig",
            ["cards" => $this->deck->twigArray()]);
    }
    /**
     * @Route("/card/deck/shuffle", name="card-deck-shuffle")
     */
    public function cardDeckShuffleRoute(SessionInterface $session): Response
    {
        $this->deck = new TwigDeck($session->get("cards") ?? []);
        $this->deck->shuffleCards();
        $session->set("cards", $this->deck->toArray());
        return $this->redirectToRoute("card-deck");
    }
    /**
     * @Route("/card/deck/draw/{nrOfCards}", name="card-deck-draw")
     */
    public function cardDeckDrawRoute(int $nrOfCards, SessionInterface $session): Response
    {
        $this->deck = new TwigDeck($session->get("cards") ?? []);
        $drawnDeck = new TwigDeck([], $autoload=false);
        $drawnDeck->addCards($this->deck->draw($nrOfCards));
        $session->set("cards", $this->deck->toArray());
        return $this->render("displaycards.html.twig", ["cards" => $drawnDeck->twigArray()]);
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
