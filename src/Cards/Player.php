<?php

namespace App\Cards;

use App\Cards\Deck;

/**
 * Player that uses cards
 */
class Player implements \Countable, \Serializable
{
    /**
     * @var array<Card>
     */
    protected $hand = [];
    /**
     * @var string
     */
    protected $name = "";
    /**
     * @param array<Card> $cards
     * @param string $name
     */
    public function __construct(array $cards = [], string $name = "NoName")
    {
        $this->name = $name;
        $this->addCards($cards);
    }

    /**
     * Size of hand
     * @return int
     */
    public function count(): int
    {
        return count($this->hand);
    }

    /**
     * Returns hand
     * @return array<Card>
     */
    public function hand(): array
    {
        return $this->hand;
    }

    /**
     * Returns array representation of object
     * @return array<string, array<int, array<string, int|string>>|string>
     */
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

    /**
     * Removes cards from hand and returns them
     * @return array<Card>
     */
    public function clear(): array
    {
        $hand = $this->hand;
        $this->hand = [];
        return $hand;
    }

    /**
     * @param array<Card> $cards
     *
     * @return void
     */
    public function addCards(array $cards): void
    {
        $this->hand = array_merge($this->hand, $cards);
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        $data = [
            "hand" => serialize($this->hand),
            "name" => serialize($this->name)
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
        $this->hand = unserialize($data["hand"]);
        $this->name = unserialize($data["name"]);
    }
}
