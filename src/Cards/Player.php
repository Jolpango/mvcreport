<?php

namespace App\Cards;

use App\Cards\Deck;

class Player implements \Countable
{
    protected $hand = [];
    protected $name = "";
    public function __construct(array $cards = [], string $name = "NoName")
    {
        $this->name = $name;
        foreach ($cards as $card) {
            array_push($this->hand, $card);
        }
    }

    public function count(): int
    {
        return count($this->hand);
    }

    public function toArray(): array
    {
        $returnArray = [];
        foreach ($this->hand as $card) {
            array_push($returnArray, ["value" => $card->getValue(), "suit" => $card->getSuit()]);
        }
        $returnDict = [
            "name" => $this->name,
            "hand" => $returnArray
        ];
        return $returnDict;
    }
}
