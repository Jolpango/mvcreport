<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class TwigDeck
 */
class TwigDeckTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $deck = new TwigDeck();
        $this->assertEquals(0, count($deck));
        $this->assertEquals([], $deck->toArray());
    }
    /**
     * Duplicates object with from and to array
     * @return void
     */
    public function testToFromArray(): void
    {
        $deck = new TwigDeck();
        $deck->addCards([
            new CardGraphic(1, "h"),
            new CardGraphic(1, "h"),
            new CardGraphic(1, "h")
        ]);
        $deck2 = TwigDeck::fromArray($deck->toArray());
        $this->assertEquals($deck, $deck2);
        $twigArrayExp = [
            [
                "value" => 1,
                "suit" => "h",
                "toString" => "Ace of h",
                "cssClass" => "ace h"
            ],
            [
                "value" => 1,
                "suit" => "h",
                "toString" => "Ace of h",
                "cssClass" => "ace h"
            ],
            [
                "value" => 1,
                "suit" => "h",
                "toString" => "Ace of h",
                "cssClass" => "ace h"
            ]
        ];
        $this->assertEquals($twigArrayExp, $deck2->twigArray());
    }
}
