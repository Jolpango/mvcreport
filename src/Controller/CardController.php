<?php

namespace App\Controller;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
use App\Cards\CardGraphic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardController extends AbstractController
{
    private $deck;
    private function loadFromSession(SessionInterface $session)
    {
        // If there are no cards in session. load a new deck
        if ($session->get("cards")) {
            $this->deck = TwigDeck::fromArray($session->get("cards") ?? []);
        } else {
            $this->deck = new TwigDeck($newDeck = true);
        }
    }
    private function saveToSession(SessionInterface $session)
    {
        $session->set("cards", $this->deck->toArray());
    }
    /**
     * @Route("/card", name="card")
     */
    public function cardIndexRoute(): Response
    {
        return $this->render("card.html.twig");
    }
    /**
     * @Route("/card/deck/reset", name="card-deck-reset")
     */
    public function cardResetRoute(SessionInterface $session): Response
    {
        $this->deck = new TwigDeck($newDeck = true);
        $this->saveToSession($session);
        return $this->redirectToRoute("card");
    }
    /**
     * @Route("/card/deck", name="card-deck")
     */
    public function cardDeckRoute(SessionInterface $session): Response
    {
        $this->loadFromSession($session);
        $this->saveToSession($session);
        return $this->render("displaycards.html.twig", ["cards" => $this->deck->twigArray()]);
    }
    /**
     * @Route("/card/deck/shuffle", name="card-deck-shuffle")
     */
    public function cardDeckShuffleRoute(SessionInterface $session): Response
    {
        $this->addFlash("messages", "Kortleken blandades");
        $this->loadFromSession($session);
        $this->deck->shuffleCards();
        $this->saveToSession($session);
        return $this->redirectToRoute("card-deck");
    }
    /**
     * @Route("/card/deck/draw/{nrOfCards}", name="card-deck-draw")
     */
    public function cardDeckDrawRoute(int $nrOfCards, SessionInterface $session): Response
    {
        $this->loadFromSession($session);
        $drawnDeck = new TwigDeck();
        $drawnDeck->addCards($this->deck->draw($nrOfCards));
        $this->saveToSession($session);
        $this->addFlash("messages", "Du drog " . $nrOfCards . " kort");
        $this->addFlash("messages", "Det finns nu " . count($this->deck) . " kort kvar i leken");
        return $this->render("carddraw.html.twig", ["cards" => $drawnDeck->twigArray()]);
    }
    /**
     * @Route("/card/deck/deal/{nrOfPlayers}/{nrOfCards}", name="card-deck-deal")
     */
    public function cardDeckDealRoute(int $nrOfPlayers, int $nrOfCards, SessionInterface $session): Response
    {
        $this->loadFromSession($session);
        $players = [];
        $playerCards = [];
        for ($i = 0; $i < $nrOfPlayers; $i++) {
            array_push($players, new TwigPlayer($this->deck->draw($nrOfCards), "Spelare " . $i));
        }
        foreach ($players as $player) {
            array_push($playerCards, $player->twigArray());
        }
        $this->saveToSession($session);
        $this->addFlash("messages", "Det har delats ut " . $nrOfCards . " kort till " . $nrOfPlayers . " spelare.");
        $this->addFlash("messages", "Det finns nu " . count($this->deck) . " kort kvar i leken");
        return $this->render("cardplayers.html.twig", ["cards" => $this->deck->twigArray(), "players" => $playerCards]);
    }
    /**
     * @Route("/card/deck2", name="card-deck-joker")
     */
    public function cardDeckJokerRoute(): Response
    {
        $jokerDeck = new TwigDeck($newDeck = true);
        for ($i = 0; $i < 2; $i++) {
            $jokerDeck->addCard(new CardGraphic(25, "Joker"));
        }
        return $this->render("displaycards.html.twig", ["cards" => $jokerDeck->twigArray()]);
    }
}
