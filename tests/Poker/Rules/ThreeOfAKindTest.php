<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class Straight
 */
class ThreeOfAKindTest extends TestCase
{
    /**
     * Tests with an expected successful result.
     * @return void
     */
    public function testSuccess(): void
    {
        $rank = 1;
        $rule = new ThreeOfAKind($rank);
        $expected = 5;
        $hand = [
            new Card(1, "h"),
            new Card(5, "h"),
            new Card(2, "h"),
            new Card(2, "sdf"),
            new Card($expected, "g"),
            new Card($expected, "h"),
            new Card($expected, "hweer"),
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
        $rule = new ThreeOfAKind();
        $hand = [
            new Card(1, "h"),
            new Card(2, "sdf"),
            new Card(2, "g"),
            new Card(4, "h"),
            new Card(65, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals(false, $point);
    }
}
