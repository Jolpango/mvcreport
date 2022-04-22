<?php

namespace App\Cards;

use App\Cards\Deck;

class Player implements \Countable, \Serializable
{
    protected $hand = [];
    protected $name = "";
    public function __construct(array $cards = [], string $name = "NoName")
    {
        $this->name = $name;
        $this->addCards($cards);
    }

    public function count(): int
    {
        return count($this->hand);
    }

    public function hand(): array
    {
        return $this->hand;
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

    public function clear(): array
    {
        $hand = $this->hand;
        $this->hand = [];
        return $hand;
    }

    public function addCards(array $cards)
    {
        $this->hand = array_merge($this->hand, $cards);
    }

    public function serialize()
    {
        $data = [
            "hand" => serialize($this->hand),
            "name" => serialize($this->name)
        ];
        return serialize($data);
    }
    public function unserialize($data)
    {
        $data = unserialize($data);
        $this->hand = unserialize($data["hand"]);
        $this->name = unserialize($data["name"]);
    }
}
