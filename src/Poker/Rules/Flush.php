<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Flush
 */
class Flush extends Rule
{
    public function __construct(int $rank = 6)
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
        usort($cards, function (Card $a, Card $b) {
            return strcmp($a->getSuit(), $b->getSuit());
        });
        $size = count($cards);
        $counter = 0;
        for ($i = 1; $i < $size && $counter < 5; $i++) {
            if ($cards[$i]->getSuit() === $cards[$i - 1]->getSuit()) {
                $counter++;
            } else {
                $counter = 0;
            }
        }
        if ($counter >= 5) {
            return new Point($this->rank, 1, "Flush");
        }
        return false;
    }
}
