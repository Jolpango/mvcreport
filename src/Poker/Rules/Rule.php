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
    public function __construct(int $rank)
    {
        $this->rank = $rank;
    }
    /**
     * Returns the value of a hand.
     * @param array<Card> $cards
     *
     * @return Point
     */
    public function calculate(array $cards): Point|bool
    {
        return false;
    }

    /**
     * @param array<Card> $cards
     *
     * @return array<Card>
     */
    protected function sortCardsDescending(array $cards): array
    {
        usort($cards, function (Card $a, Card $b) {
            if ($a->getValue() == $b->getValue()) {
                return 0;
            }
            return $a->getValue() < $b->getValue() ? 1 : -1;
        });
        return $cards;
    }
}
