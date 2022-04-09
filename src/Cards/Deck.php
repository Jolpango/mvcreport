<?php

namespace App\Cards;
use App\Cards\Card;

class Deck implements IDeck
{    
    protected array $cards = [];
    public function __construct(array $toLoad=[], $autoload) {
        if ($autoload) {
            if (count($toLoad) > 0) {
                foreach ($toLoad as $card) {
                    array_push($this->cards, new Card($card["value"], $card["suit"]));
                }
            } else {
                foreach ($this->suits as $suit) {
                    foreach (Card::$valueToString as $k => $v) {
                        array_push($this->cards, new Card($k, $suit));
                    }
                }
                // $this->shuffleCards();
            }
        }
    }

    public function addCard(Card $card) {
        array_push($this->cards, $card);
        $this->shuffleCards();
    }

    public function addCards(array $cards) {
        $this->cards = array_merge($this->cards, $cards);
        $this->shuffleCards();
    }

    public function shuffleCards(): bool {
        return shuffle($this->cards);
    }

    public function draw($count) {
        //Removes and return $count nr of cards from the end
        return array_splice($this->cards, count($this->cards) - $count, $count);
    }

    public function toArray(): array {
        $returnArray = [];
        foreach ($this->cards as $card) {
            array_push($returnArray, ["value" => $card->getValue(), "suit" => $card->getSuit()]);
        }
        return $returnArray;
    }
}
