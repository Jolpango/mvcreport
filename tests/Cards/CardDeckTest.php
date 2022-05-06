<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test cases for class CardGame
 */
class CardDeckTest extends TestCase
{
    /**
     * Test creation
     * @return void
     */
    public function testCreateEmpty(): void {
        $deck = new Deck();
        $this->assertEquals(0, count($deck));
        $this->assertEquals([], $deck->toArray());
    }
}
