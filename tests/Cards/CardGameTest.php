<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardGame
 */
class CardGameTest extends TestCase
{
    /**
     * Test creation
     * @return void
     */
    public function testCreation(): void {
        $game = new CardGame();
        $this->assertInstanceOf("\App\Cards\CardGame", $game);
    }
    public function testProcessDealStart(): void {
        $game = new CardGame();
        $request = new Request();
        $request->set("type", "DEAL_START");
        $game->processRequest($request);
        $this->assertEquals("game/player.html.twig", $game->renderPath());
    }
}
