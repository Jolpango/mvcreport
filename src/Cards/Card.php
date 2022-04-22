<?php

namespace App\Cards;

class Card implements \Serializable
{
    public static array $suits = [
        "Hearts",
        "Diamonds",
        "Spades",
        "Cloves"
        ];
    public static array $valueToString = [
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
        12 => "Queen",
        13 => "King",
        25 => "Joker"
        ];
    protected int $value;
    protected string $suit;

    public static function setValueArray(array $newValues)
    {
        Card::$valueToString = $newValues;
    }

    public function __construct($value, $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    public function toString(): string
    {
        if ($this->suit === "Joker") {
            return $this->suit;
        }
        return "{$this->value} of {$this->suit}";
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function serialize()
    {
        $data = [
            "value" => $this->value,
            "suit" => $this->suit
        ];
        return serialize($data);
    }
    public function unserialize($data)
    {
        $data = unserialize($data);
        $this->suit = $data["suit"];
        $this->value = $data["value"];
    }
}
