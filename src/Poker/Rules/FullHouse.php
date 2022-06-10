<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class FullHouse
 */
class FullHouse extends Rule
{
    public function __construct(int $rank = 7)
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
        $threes = [];
        $size = count($cards);
        for ($i = 1; $i < $size; $i++) {
            if ($cards[$i]->getValue() === $cards[$i - 1]->getValue()) {
                $counter++;
            } elseif ($cards[$i]->getValue() !== $cards[$i - 1]->getValue()) {
                $counter = 0;
            }
            if ($counter === 2) {
                array_push($pairs, $cards[$i]->getValue());
            } elseif ($counter === 3) {
                array_push($threes, $cards[$i]->getValue());
            }
        }
        $fullHouseTotal = 0;
        for ($i = 0; $i < count($threes); $i++) {
            for ($j = 0; $j < count($pairs); $j++) {
                if ($threes[$i] != $pairs[$j]) {
                    $val = $threes[$i] * 3 + $pairs[$j] * 2;
                    if ($fullHouseTotal < $val) {
                        $fullHouseTotal = $val;
                    }
                }
            }
        }
        if ($fullHouseTotal) {
            return new Point($this->rank, $fullHouseTotal, "Full house");
        }
        return false;
    }
}
