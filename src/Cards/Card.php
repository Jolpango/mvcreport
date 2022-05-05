<?php

namespace App\Cards;

/**
 * Class Card, represents basic playing card
 */
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

    /**
     * @param array $newValues
     *
     * @return void
     */
    public static function setValueArray(array $newValues): void
    {
        Card::$valueToString = $newValues;
    }

    /**
     * @param mixed $value
     * @param mixed $suit
     */
    public function __construct($value, $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        if ($this->suit === "Joker") {
            return $this->suit;
        }
        return "{$this->value} of {$this->suit}";
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        $data = [
            "value" => $this->value,
            "suit" => $this->suit
        ];
        return serialize($data);
    }
    /**
     * @param string $data
     *
     * @return void
     */
    public function unserialize(string $data): void
    {
        $data = unserialize($data);
        $this->suit = $data["suit"];
        $this->value = $data["value"];
    }
}
