<?php

namespace App\Cards;
use App\Cards\CardGraphic;

class TwigDeck extends Deck
{
    public function __construct(array $toLoad=[], $autoload=true) {
        // Load basic playing deck
        if ($autoload) {
            if (count($toLoad) > 0) {
                foreach ($toLoad as $card) {
                    array_push($this->cards, new CardGraphic($card["value"], $card["suit"]));
                }
            } else {
                foreach (Card::$suits as $suit) {
                    foreach (Card::$valueToString as $k => $v) {
                        array_push($this->cards, new CardGraphic($k, $suit));
                    }
                }
                // $this->shuffleCards();
            }
        }
    }

    public function twigArray(): array {
        $returnArray = [];
        foreach ($this->cards as $card) {
            $cardDict = [
                "value" => $card->getValue(),
                "suit" => $card->getSuit(),
                "toString" => $card->toString(),
                "cssClass" => $card->toCSSClass()
            ];
            array_push($returnArray, $cardDict);
        }
        return $returnArray;
    }
}
