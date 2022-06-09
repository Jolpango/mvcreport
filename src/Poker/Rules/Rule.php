<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Rule
 */
class Rule
{
    protected int $rank;
    /**
     * @param int $rank
     */
    public function __construct(int $rank) {
        $this->rank = $rank;
    }
    /**
     * Returns the value of a hand.
     * @param array<Card> $cards
     * 
     * @return Point
     */
    public function calculate(array $cards): Point|bool {
        return false;
    }
}
