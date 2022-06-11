<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class RoyalFlushTest
 */
class RoyalFlushTest extends TestCase
{
    /**
     * Tests with an expected successful result.
     * @return void
     */
    public function testSuccess(): void
    {
        $rank = 1;
        $rule = new RoyalFlush($rank);
        $hand = [
            new Card(11, "h"),
            new Card(12, "h"),
            new Card(13, "h"),
            new Card(10, "h"),
            new Card(14, "h"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals($rank, $point->getPoint());
        $this->assertEquals(1, $point->getTieBreakerPoint());
    }
    /**
     * Tests with an expected fail
     * @return void
     */
    public function testFail(): void
    {
        $rule = new RoyalFlush();
        $hand = [
            new Card(10, "h"),
            new Card(10, "h"),
            new Card(13, "hs"),
            new Card(13, "h"),
            new Card(14, "h"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals(false, $point);
    }
}
