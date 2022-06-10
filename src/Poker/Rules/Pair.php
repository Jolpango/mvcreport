<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Pair
 */
class Pair extends Rule
{
    public function __construct(int $rank = 2)
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
        $pair = 0;
        $size = count($cards);
        for ($i = 1; $i < $size && !$pair; $i++) {
            if ($cards[$i]->getValue() === $cards[$i - 1]->getValue()) {
                $pair = $cards[$i]->getValue();
            }
        }
        if ($pair) {
            return new Point($this->rank, $pair, "Pair!");
        }
        return false;
    }
}
