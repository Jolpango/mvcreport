<?php

namespace App\Cards;

class Card {
    protected int $value;
    protected string $suit;

    public function __construct($value, $suit) {
        $this->value = $value;
        $this->suit = $suit;
    }

    public function toString(): string {
        return "{$this->value} of {$this->suit}";
    }
}

class CardGraphic extends Card {
    private static array $valueToString = [
        1 => "Ace",
        2 => "Two",
        3 => "Three",
        4 => "Four",
        5 => "Five",
        6 => "Six",
        7 => "Seven",
        8 => "Eight",
        9 => "Nine",
        10 => "Ten",
        11 => "Jack",
        13 => "Queen",
        14 => "King",
        25 => "Joker"
    ];

    public function __construct($value, $suit) {
        parent::__construct($value, $suit);
    }

    public function toString(): string {
        $valueString = self::$valueToString[$this->value] ?? $this->value;
        return "{$valueString} of {$this->suit}";
    }

    public function toCSSClass(): string {
        $valueClass = strtolower(self::$valueToString[$this->value]) ?? $this->value;
        $suitClass = strtolower($this->suit);
        return ".{$stringValue} .{$stringSuit}";
    }
}
