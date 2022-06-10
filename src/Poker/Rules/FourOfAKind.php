<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Straight
 */
class FourOfAKind extends Rule
{
    public function __construct(int $rank = 8)
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
        $cards = $this->sortCardsDescending($cards);
        $counter = 0;
        $biggestCard = $cards[0]->getValue();
        $size = count($cards);
        for ($i = 1; $i < $size && $counter < 4; $i++) {
            if ($cards[$i]->getValue() === $cards[$i - 1]->getValue()) {
                $counter++;
            } elseif ($cards[$i]->getValue() !== $cards[$i - 1]->getValue()) {
                $counter = 0;
                $biggestCard = $cards[$i]->getValue();
            }
        }
        if ($counter >= 4) {
            return new Point($this->rank, $biggestCard, "Four of a kind!");
        }
        return false;
    }
}
