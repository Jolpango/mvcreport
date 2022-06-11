<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class FlushTest
 */
class FlushTest extends TestCase
{
    /**
     * Tests with an expected successful result.
     * @return void
     */
    public function testSuccess(): void
    {
        $rank = 1;
        $rule = new Flush($rank);
        $hand = [
            new Card(11, "h"),
            new Card(5, "h"),
            new Card(3, "h"),
            new Card(45, "h"),
            new Card(45, "d"),
            new Card(45, "f"),
            new Card(45, "s"),
            new Card(67, "h"),
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
        $rule = new Flush();
        $hand = [
            new Card(11, "h"),
            new Card(12, "h"),
            new Card(13, "hs"),
            new Card(10, "h"),
            new Card(14, "h"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals(false, $point);
    }
}
