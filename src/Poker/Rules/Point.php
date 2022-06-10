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
    private string $message;
    /**
     * Point is compared to other rules
     * Tiebreaker point is individual per rule. It is only if it is a tie
     * @param int $point
     * @param int $tieBreakerPoint
     */
    public function __construct(int $point, int $tieBreakerPoint, string $message)
    {
        $this->message = $message;
        $this->point = $point;
        $this->tieBreakerPoint = $tieBreakerPoint;
    }
    /**
     * @return int
     */
    public function getPoint(): int
    {
        return $this->point;
    }
    /**
     * @return int
     */
    public function getTieBreakerPoint(): int
    {
        return $this->tieBreakerPoint;
    }
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
