<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Straight
 */
class Straight extends Rule
{
    public function __construct(int $rank = 5)
    {
        parent::__construct($rank);
    }
    /**
     * Returns the point of a hand
     * @param array<Card> $cards
     *
     * @return Point|bool
     */
    public function calculate(array $cards): Point|bool
    {
        $size = count($cards);
        for ($i = 0; $i < $size; $i++) {
            if ($cards[$i]->getValue() === 14) {
                array_push($cards, new Card(1, $cards[$i]->getSuit()));
            }
        }
        $cards = $this->sortCardsDescending($cards);
        $counter = 0;
        $biggestCard = $cards[0]->getValue();
        $size = count($cards);
        for ($i = 1; $i < $size && $counter < 5; $i++) {
            if ($cards[$i]->getValue() + 1 === $cards[$i - 1]->getValue()) {
                $counter++;
            } elseif ($cards[$i]->getValue() !== $cards[$i - 1]->getValue()) {
                $counter = 0;
                $biggestCard = $cards[$i]->getValue();
            }
        }
        if ($counter >= 5) {
            return new Point($this->rank, $biggestCard, "Straight!");
        }
        return false;
    }
}
