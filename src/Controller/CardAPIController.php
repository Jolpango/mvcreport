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

class CardAPIController extends AbstractController
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
     * @Route("/card/api/deck", name="card-api-deck")
     */
    public function cardAPIDeckRoute(SessionInterface $session): JsonResponse
    {
        $this->loadFromSession($session);
        $this->saveToSession($session);
        $responseArray = $this->deck->toArray();
        sort($responseArray); //sorts by value
        usort($responseArray, function ($a, $b) {
            return strcmp($a["suit"], $b["suit"]);
        }); //then sort by suit
        return new JsonResponse(array('cards' => $responseArray));
    }
    /**
     * @Route("/card/api/deck/shuffle", name="card-api-deck-shuffle")
     */
    public function cardAPIDeckShuffleRoute(SessionInterface $session): JsonResponse
    {
        $this->loadFromSession($session);
        $this->deck->shuffleCards();
        $this->saveToSession($session);
        return new JsonResponse(array('cards' => $this->deck->toArray()));
    }
}
