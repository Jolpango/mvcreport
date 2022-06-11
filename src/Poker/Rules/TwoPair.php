<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class TwoPair
 */
class TwoPair extends Rule
{
    public function __construct(int $rank = 3)
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
        $pairs = [];
        $biggestCard = $cards[0]->getValue();
        $size = count($cards);
        for ($i = 1; $i < $size && $counter < 2; $i++) {
            if (array_search($cards[$i]->getValue(), $pairs) === false && $cards[$i]->getValue() === $cards[$i - 1]->getValue()) {
                $counter++;
                array_push($pairs, $cards[$i]->getValue());
            }
        }
        if ($counter >= 2) {
            return new Point($this->rank, $pairs[0], "Two pairs");
        }
        return false;
    }
}
