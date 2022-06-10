<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class TwigPlayer
 */
class TwigPlayerTest extends TestCase
{
    public function testTwigArray(): void
    {
        $player = new TwigPlayer();
        $player->addCards([new CardGraphic(14, "h"), new CardGraphic(14, "h"), new CardGraphic(14, "h")]);
        $twigArrayHand = [
            [
                "value" => 14,
                "suit" => "h",
                "toString" => "Ace of h",
                "cssClass" => "ace h"
            ],
            [
                "value" => 14,
                "suit" => "h",
                "toString" => "Ace of h",
                "cssClass" => "ace h"
            ],
            [
                "value" => 14,
                "suit" => "h",
                "toString" => "Ace of h",
                "cssClass" => "ace h"
            ]
        ];
        $exp = [
            "name" => "NoName",
            "hand" => $twigArrayHand
        ];
        $this->assertEquals($exp, $player->twigArray());
    }
}
