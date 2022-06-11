<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class HighHand
 */
class HighHandTest extends TestCase
{
    /**
     * Tests with an expected successful result.
     * @return void
     */
    public function testSuccess(): void
    {
        $rank = 1;
        $rule = new HighHand($rank);
        $expected = 5;
        $hand = [
            new Card(1, "h"),
            new Card(2, "h"),
            new Card(2, "h"),
            new Card(2, "h"),
            new Card($expected, "g"),
            new Card($expected, "g"),
            new Card($expected, "h"),
            new Card($expected, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals($rank, $point->getPoint());
        $this->assertEquals($expected, $point->getTieBreakerPoint());
    }
}
