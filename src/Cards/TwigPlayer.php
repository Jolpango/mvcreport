<?php

namespace App\Cards;

use App\Cards\Player;

class TwigPlayer extends Player
{
    public function twigArray(): array
    {
        $returnArray = [];
        foreach ($this->hand as $card) {
            $cardDict = [
                "value" => $card->getValue(),
                "suit" => $card->getSuit(),
                "toString" => $card->toString(),
                "cssClass" => $card->toCSSClass()
            ];
            array_push($returnArray, $cardDict);
        }
        $returnDict = [
            "name" => $this->name,
            "hand" => $returnArray
        ];
        return $returnDict;
    }
}
