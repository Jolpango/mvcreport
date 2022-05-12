<?php

namespace App\Cards;

use App\Cards\Card;

/**
 * Class Deck. Implements IDeck. Is Countable and Serializable
 */
class Deck implements IDeck, \Countable, \Serializable
{
    /**
     * @var array<Card>
     */
    protected array $cards = [];
    /**
     * @param bool $newDeck
     */
    public function __construct($newDeck = false)
    {
        if ($newDeck) {
            foreach (Card::$suits as $suit) {
                foreach (Card::$valueToString as $k => $v) {
                    if ($v !== "Joker") {
                        array_push($this->cards, new Card($k, $suit));
                    }
                }
            }
            // $this->shuffleCards();
        }
    }

    /**
     * @param array<string, string|int> $toLoad
     *
     * @return Deck
     */
    public static function fromArray(array $toLoad): Deck
    {
        $deck = new Deck();
        foreach ($toLoad as $card) {
            array_push($deck->cards, new Card($card["value"], $card["suit"]));
        }
        return $deck;
    }

    /**
     * @param Card $card
     *
     * @return void
     */
    public function addCard(Card $card): void
    {
        array_push($this->cards, $card);
        // $this->shuffleCards();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->cards);
    }

    /**
     * @param array<Card> $cards
     *
     * @return void
     */
    public function addCards(array $cards): void
    {
        $this->cards = array_merge($this->cards, $cards);
        // $this->shuffleCards();
    }

    /**
     * Shuffles cards. Returns bool representing success or fail
     * @return bool
     */
    public function shuffleCards(): bool
    {
        return shuffle($this->cards);
    }

    /**
     * Returns $count cards. Removes them from this object
     * @param int $count
     *
     * @return array<Card>
     */
    public function draw(int $count): array
    {
        //Removes and return $count nr of cards from the end
        return array_splice($this->cards, count($this->cards) - $count, $count);
    }

    /**
     * Array representation of this object
     * @return array<int, array<string, int|string>>
     */
    public function toArray(): array
    {
        $returnArray = [];
        foreach ($this->cards as $card) {
            array_push($returnArray, ["value" => $card->getValue(), "suit" => $card->getSuit()]);
        }
        return $returnArray;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        $data = [
            "cards" => serialize($this->cards)
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
        $this->cards = unserialize($data["cards"]);
    }
}
