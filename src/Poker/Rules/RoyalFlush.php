<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class RoyalFlush
 */
class RoyalFlush extends Rule
{
    public function __construct(int $rank = 10)
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
        for ($i = 1; $i < $size && $counter < 5; $i++) {
            if ($cards[$i]->getValue() + 1 === $cards[$i - 1]->getValue()) {
                if ($cards[$i]->getSuit() === $cards[$i - 1]->getSuit()) {
                    $counter++;
                }
            } elseif ($cards[$i]->getValue() !== $cards[$i - 1]->getValue()) {
                $counter = 0;
                $biggestCard = $cards[$i]->getValue();
            }
        }
        if ($counter >= 5 && $biggestCard === 14) {
            return new Point($this->rank, 1, "Royal flush");
        }
        return false;
    }
}
