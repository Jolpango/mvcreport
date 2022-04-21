<?php

namespace App\Cards;

use App\Cards\Player;

function possibleCombinations($length) {
    $totalCombos = pow(2, $length);
    
    $sequences = array();

    for($x = 0; $x < $totalCombos; $x++) {
        $sequences[$x] = str_split(str_pad(decbin($x), $length, 0, STR_PAD_LEFT));
    }
    return $sequences;
}

class PointSystem
{
    public static function Points21(array $cards): array {
        $possiblePoints = [];
        $arrayOfValues = [];
        $handWithoutAces = array_filter($cards, function ($card) {
            return $card->getValue() !== 1;
        });
        $aces = array_filter($cards, function ($card) {
            return $card->getValue() === 1;
        });
        $sumWithoutAces = 0;
        foreach($handWithoutAces as $card) {
            $sumWithoutAces += $card->getValue();
        }
        if (count($aces) === 0) {
            array_push($possiblePoints, $sumWithoutAces);
        } else {
            $combinations = possibleCombinations(count($aces));
            foreach($combinations as $combination) {
                $sum = 0;
                foreach($combination as $big) {
                    if($big) {
                        $sum += 14;
                    } else {
                        $sum += 1;
                    }
                }
                array_push($possiblePoints, $sum + $sumWithoutAces);
            }
        }
        $possiblePoints = array_unique($possiblePoints);
        return $possiblePoints;
    }

    public static function BestPoint($points): int {
        $bestPoint = 0;
        foreach($points as $point) {
            if ($point <= 21 && $point > $bestPoint) {
                $bestPoint = $point;
            }
        }
        return $bestPoint;
    }
}
