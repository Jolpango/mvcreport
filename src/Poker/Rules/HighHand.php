<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class HighHand
 */
class HighHand extends Rule
{
    public function __construct(int $rank=1)
    {
        parent::__construct($rank);
    }
    /**
     * Returns the point of a hand
     * @param array<Card> $cards
     * 
     * @return Point|bool
     */
    public function calculate(array $cards): Point|bool {
        usort($cards, function(Card $a,Card $b) {
            if ($a->getValue() < $b->getValue()) {
                return -1;
            }
            if ($a->getValue() === $b->getValue()) {
                return 0;
            }
            return 1;
        });
        return new Point($this->rank, $cards[0]->getValue());
    }
}
