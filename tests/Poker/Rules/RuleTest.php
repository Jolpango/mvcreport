<?php

namespace App\Poker\Rules;

use PHPUnit\Framework\TestCase;
use App\Cards\Card;

/**
 * Test cases for class Rule
 */
class RuleTest extends TestCase
{
    /**
     * Tests with an expected fail
     * @return void
     */
    public function testFail(): void
    {
        $rule = new Rule(5);
        $hand = [
            new Card(1, "h"),
            new Card(2, "sdf"),
            new Card(2, "g"),
            new Card(2, "g"),
            new Card(4, "h"),
            new Card(65, "hweer"),
        ];
        $point = $rule->calculate($hand);
        $this->assertEquals(false, $point);
    }
}
