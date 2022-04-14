<?php

namespace App\Cards;

class CardGraphic extends Card
{
    public function __construct($value, $suit)
    {
        parent::__construct($value, $suit);
    }

    public function toString(): string
    {
        if ($this->suit === "Joker") {
            return $this->suit;
        }
        $valueString = Card::$valueToString[$this->value] ?? $this->value;
        return "{$valueString} of {$this->suit}";
    }

    public function toCSSClass(): string
    {
        $valueClass = strtolower(Card::$valueToString[$this->value]) ?? $this->value;
        $suitClass = strtolower($this->suit);
        return "{$valueClass} {$suitClass}";
    }
}
