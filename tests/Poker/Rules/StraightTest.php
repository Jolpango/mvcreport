<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class Straight
 */
class StraightTest extends TestCase
{
    /**
     * Tests with an expected successful result.
     * @return void
     */
    public function testSuccess(): void
    {
        $rank = 1;
        $rule = new Straight($rank);
        $high = 5;
        $hand = [
            new Card(1, "h"),
            new Card(14, "h"),
            new Card(2, "sdf"),
            new Card(3, "g"),
            new Card(4, "h"),
            new Card($high, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals($rank, $point->getPoint());
        $this->assertEquals($high, $point->getTieBreakerPoint());
    }
    /**
     * Tests with an expected fail
     * @return void
     */
    public function testFail(): void
    {
        $rule = new Straight();
        $hand = [
            new Card(1, "h"),
            new Card(2, "sdf"),
            new Card(3, "g"),
            new Card(4, "h"),
            new Card(65, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals(false, $point);
    }
}
