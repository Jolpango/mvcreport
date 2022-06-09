<?php

namespace App\Poker\Rules;

use App\Cards\Card;

/**
 * class Point
 */
class Point
{
    private int $point;
    private int $tieBreakerPoint;
    /**
     * Point is compared to other rules
     * Tiebreaker point is individual per rule. It is only if it is a tie
     * @param int $point
     * @param int $tieBreakerPoint
     */
    public function __construct(int $point,int $tieBreakerPoint)
    {
        $this->point = $point;
        $this->tieBreakerPoint = $tieBreakerPoint;
    }
    /**
     * @return int
     */
    public function getPoint(): int {
        return $this->point;
    }
    /**
     * @return int
     */
    public function getTieBreakerPoint(): int {
        return $this->tieBreakerPoint;
    }
}
