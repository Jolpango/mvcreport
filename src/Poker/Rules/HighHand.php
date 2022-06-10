<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class HighHand
 */
class HighHand extends Rule
{
    public function __construct(int $rank = 1)
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
        return new Point($this->rank, $cards[0]->getValue(), "High card");
    }
}
