<?php

namespace App\Cards;

/**
 * CSS Graphic extenstion of Card
 */
class CardGraphic extends Card
{
    /**
     * @param int $value
     * @param string $suit
     */
    public function __construct(int $value, string $suit)
    {
        parent::__construct($value, $suit);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        if ($this->suit === "Joker") {
            return $this->suit;
        }
        $valueString = Card::$valueToString[$this->value] ?? $this->value;
        return "{$valueString} of {$this->suit}";
    }

    /**
     * @return string
     */
    public function toCSSClass(): string
    {
        $valueClass = strtolower(self::$valueToString[$this->value] ?? $this->value);
        $suitClass = strtolower($this->suit);
        return "{$valueClass} {$suitClass}";
    }
}
