<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test cases for class CardGame
 */
class PointSystemTest extends TestCase
{
    /**
     * Tests points21 function
     * @return void
     */
    public function testPointCalculationNoAces(): void {
        $cards = [
            new Card(2, "Hearts"),
            new Card(5, "Hearts"),
            new Card(6, "Hearts"),
            new Card(8, "Hearts")
        ];
        $exp = 2 + 5 + 6 + 8;
        $this->assertEquals(PointSystem::points21($cards)[0], $exp);
    }
    public function testPointCalculationAces(): void {
        $cards = [
            new Card(2, "Hearts"),
            new Card(5, "Hearts"),
            new Card(6, "Hearts"),
            new Card(1, "Hearts")
        ];
        $exp = [2 + 5 + 6 + 14, 2 + 5 + 6 + 1];
        $this->assertEquals(PointSystem::points21($cards), $exp);
    }
    public function testBestPoint(): void {
        $points = [0, 1, 6, 29, 3, 19];
        $exp = 19;
        $this->assertEquals($exp, PointSystem::bestPoint($points));
    }
}
