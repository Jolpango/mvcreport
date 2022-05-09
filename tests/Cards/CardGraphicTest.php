<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Card
 */
class CardGraphicTest extends TestCase
{
    /**
     * Test css class function
     * @return void
     */
    public function testCSSClass(): void
    {
        $card = new CardGraphic(1, "Hearts");
        $exp = "ace hearts";
        $this->assertEquals($exp, $card->toCSSClass());
    }
    /**
     * Test css class function
     * @return void
     */
    public function testToString(): void
    {
        $card = new CardGraphic(1, "Hearts");
        $exp = "Ace of Hearts";
        $this->assertEquals($exp, $card->toString());
    }
    public function testToStringJoker(): void
    {
        $card = new CardGraphic(1, "Joker");
        $exp = "Joker";
        $this->assertEquals($exp, $card->toString());
    }
}
