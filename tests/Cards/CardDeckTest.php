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
    public function testCreateEmpty(): void
    {
        $deck = new Deck();
        $this->assertEquals(0, count($deck));
        $this->assertEquals([], $deck->toArray());
    }
    /**
     * Test serialization of object
     * @return void
     */
    public function testSerializiation(): void
    {
        $deck = new Deck(true);
        $deckString = serialize($deck);
        $deck2 = unserialize($deckString);
        $this->assertEquals($deck, $deck2);
    }
    /**
     * Duplicates object with from and to array
     * @return void
     */
    public function testToFromArray(): void
    {
        $deck = new Deck(true);
        $deck2 = Deck::fromArray($deck->toArray());
        $this->assertEquals($deck, $deck2);
        $this->assertEquals(52, count($deck));
    }
    /**
     * Test adding a card
     * @return void
     */
    public function testAddCard(): void
    {
        $deck = new Deck(false);
        $deck->addCard(new Card(1, "Hearts"));
        $this->assertEquals([["value" => 1, "suit" => "Hearts"]], $deck->toArray());
        $deck->addCard(new Card(2, "Hearts"));
        $this->assertEquals([["value" => 1, "suit" => "Hearts"], ["value" => 2, "suit" => "Hearts"]], $deck->toArray());
    }
    public function testAddCards(): void
    {
        $deck = new Deck(false);
        $deck->addCards([new Card(1, "Hearts"), new Card(2, "Hearts")]);
        $this->assertEquals([["value" => 1, "suit" => "Hearts"], ["value" => 2, "suit" => "Hearts"]], $deck->toArray());
    }
}
