<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class FullHouse
 */
class FullHouseTest extends TestCase
{
    /**
     * Tests with an expected successful result.
     * @return void
     */
    public function testSuccess(): void
    {
        $rank = 1;
        $rule = new FullHouse($rank);
        $high = 5;
        $low = 2;
        $expected = $high * 3 + $low * 2;
        $hand = [
            new Card(1, "h"),
            new Card(5, "h"),
            new Card($low, "h"),
            new Card($low, "h"),
            new Card($high, "g"),
            new Card($high, "h"),
            new Card($high, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals($rank, $point->getPoint());
        $this->assertEquals($expected, $point->getTieBreakerPoint());
    }
    /**
     * Tests with an expected fail
     * @return void
     */
    public function testFail(): void
    {
        $rule = new FullHouse();
        $hand = [
            new Card(1, "h"),
            new Card(2, "sdf"),
            new Card(2, "sdf"),
            new Card(2, "sdf"),
            new Card(2, "sdf"),
            new Card(2, "sdf"),
            new Card(2, "sdf"),
            new Card(2, "sdf"),
            new Card(4, "h"),
            new Card(65, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals(false, $point);
    }
}
