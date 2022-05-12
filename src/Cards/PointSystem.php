<?php

namespace App\Cards;

use App\Cards\Player;

/**
 * Class for calculating points from hands.
 */
class PointSystem
{
    /**
     * Calculates possible points from array of cards
     * @param array<Card> $cards
     *
     * @return array<int>
     */
    public static function points21(array $cards): array
    {
        $possiblePoints = [];
        $arrayOfValues = [];
        $handWithoutAces = array_filter($cards, function ($card) {
            return $card->getValue() !== 1;
        });
        $aces = array_filter($cards, function ($card) {
            return $card->getValue() === 1;
        });
        $sumWithoutAces = 0;
        foreach ($handWithoutAces as $card) {
            $sumWithoutAces += $card->getValue();
        }
        if (count($aces) === 0) {
            array_push($possiblePoints, $sumWithoutAces);
        } else {
            for ($i = 0; $i <= count($aces); $i++) {
                array_push($possiblePoints, $sumWithoutAces + $i * 1 + (count($aces) - $i) * 14);
            }
        }
        $possiblePoints = array_unique($possiblePoints);
        return $possiblePoints;
    }

    /**
     * Returns the best point. The highest one, but less than or equal 21
     * @param array<int> $points
     *
     * @return int
     */
    public static function bestPoint(array $points): int
    {
        $bestPoint = 0;
        foreach ($points as $point) {
            if ($point <= 21 && $point > $bestPoint) {
                $bestPoint = $point;
            }
        }
        return $bestPoint;
    }
}
