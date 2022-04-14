<?php

namespace App\Cards;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
use App\Cards\CardGraphic;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeckSessionHandler
{
    protected $deck;
    protected function loadFromSession(SessionInterface $session)
    {
        // If there are no cards in session. load a new deck
        if ($session->get("cards")) {
            $this->deck = TwigDeck::fromArray($session->get("cards") ?? []);
        } else {
            $this->deck = new TwigDeck($newDeck = true);
        }
    }
    protected function saveToSession(SessionInterface $session)
    {
        $session->set("cards", $this->deck->toArray());
    }
}
