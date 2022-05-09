<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Card
 */
class CardTest extends TestCase
{
    /**
     * Test creation
     * @return void
     */
    public function testCreateObject(): void {
        $card = new Card(2, "Hearts");
        $this->assertInstanceOf("\App\Cards\Card", $card);
        $this->assertEquals($card->getValue(), 2);
        $this->assertEquals($card->getSuit(), "Hearts");
    }
    public function testToString(): void {
        $card = new Card(2, "Hearts");
        $this->assertInstanceOf("\App\Cards\Card", $card);
        $exp = "2 of Hearts";
        $this->assertEquals($exp, $card->toString());
    }
    public function testToStringJoker(): void {
        $card = new Card(25, "Joker");
        $this->assertInstanceOf("\App\Cards\Card", $card);
        $exp = "Joker";
        $this->assertEquals($exp, $card->toString());
    }
    public function testSerializeCard(): void {
        $card = new Card(2, "Hearts");
        $serialized = serialize($card);
        $this->assertIsString($serialized);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf("\App\Cards\Card", $unserialized);
        $this->assertEquals($card->getValue(), 2);
        $this->assertEquals($card->getSuit(), "Hearts");
    }
    public function testSetValueArray(): void {
        $org = Card::$valueToString;
        $a = [0 => "test"];
        Card::setValueArray($a);
        $this->assertEquals($a, Card::$valueToString);
        Card::setValueArray($org);
    }
}
