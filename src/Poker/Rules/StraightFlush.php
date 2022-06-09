<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Straight
 */
class StraightFlush extends Rule
{
    public function __construct(int $rank=9)
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
        $counter = 0;
        $biggestCard = $cards[0]->getValue();
        $size = count($cards);
        for ($i = 1; $i < $size && $counter < 5; $i++) {
            if ($cards[$i]->getValue() + 1 === $cards[$i - 1]->getValue()) {
                if($cards[$i]->getSuit() === $cards[$i - 1]->getSuit()) {
                    $counter++;
                }
            } else if ($cards[$i]->getValue() !== $cards[$i - 1]->getValue()) {
                $counter = 0;
                $biggestCard = $cards[$i]->getValue();
            }
        }
        if ($counter >= 5) {
            return new Point($this->rank, $biggestCard);
        }
        return false;
    }
}
