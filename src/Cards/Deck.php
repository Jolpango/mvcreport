<?php

namespace App\Cards;

class Deck implements IDeck
{
    private array $cards = [];
    private array $suits = [
        "Hearts",
        "Diamonds",
        "Spades",
        "Cloves"
    ];
    public function __construct(bool $loadBasicDeck=true) {
        // Load basic playing deck
        if ($loadBasicDeck) {
            foreach ($this->suits as $suit) {
                for ($i = 0; $i < 15; $i++) {
                    array_push($this->cards, new GraphicCard($i, $suit));
                }
            }
            $this->shuffle();
        }
    }

    public function addCard(Card $card) {
        array_push($this->cards, $card);
        $this->shuffle();
    }

    public function addCards(array $cards) {
        array_push($this->cards, $cards);
        $this->shuffle();
    }

    public function shuffle(): bool {
        return shuffle($this->cards);
    }

    public function draw($count) {
        //Removes and return $count nr of cards from the end
        return array_splice($this->cards, count($this->cards) - $counts, $count);
    }
}
