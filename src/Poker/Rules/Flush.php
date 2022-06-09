<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Flush
 */
class Flush extends Rule
{
    public function __construct(int $rank=6)
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
        
        return false;
    }
}
