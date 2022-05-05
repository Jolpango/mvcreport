<?php

namespace App\Cards;

use App\Cards\Player;

/**
 * Class TwigPlayer. Extenstion of Player with functions for use with twig
 */
class TwigPlayer extends Player
{
    /**
     * Returns a representation of object for use in twig
     * @return array
     */
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
