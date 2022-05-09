<?php

namespace App\Cards;

use App\Cards\CardGraphic;

/**
 * Deck for use with twig. contains extra functions
 */
class TwigDeck extends Deck
{
    /**
     * @param bool $newDeck
     */
    public function __construct($newDeck = false)
    {
        // Load basic playing deck
        if ($newDeck) {
            foreach (Card::$suits as $suit) {
                foreach (Card::$valueToString as $k => $v) {
                    if ($v !== "Joker") {
                        array_push($this->cards, new CardGraphic($k, $suit));
                    }
                }
            }
            // $this->shuffleCards();
        }
    }

    /**
     * @param array $toLoad
     *
     * @return Deck
     */
    public static function fromArray(array $toLoad): Deck
    {
        $deck = new TwigDeck();
        foreach ($toLoad as $card) {
            array_push($deck->cards, new CardGraphic($card["value"], $card["suit"]));
        }
        return $deck;
    }

    /**
     * Returns array for use in twig templates
     * @return array
     */
    public function twigArray(): array
    {
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
