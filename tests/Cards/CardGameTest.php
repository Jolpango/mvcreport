<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

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
        $this->assertEquals([], $game->renderData());
    }
    public function testProcessDealStart(): void {
        $game = new CardGame();
        $request = Request::create("test", "POST", ["type" => "DEAL_START"]);
        $messages = $game->processRequest($request);
        $this->assertEquals("Dealing starter cards", $messages[0]);
        $this->assertEquals("Shuffling...", $messages[1]);
        $this->assertEquals("Dealing 1 card to you", $messages[2]);
        $this->assertEquals("Dealing 1 card to the bank", $messages[3]);
        $this->assertEquals("game/player.html.twig", $game->renderPath());
        $this->assertEquals("PLAYER", $game->getState());
        $errorMessage = $game->processRequest($request);
        $this->assertEquals("It is not the start of the game, you cannot do this", $errorMessage[0]);
    }
    public function testProcessPlayerDraw(): void {
        $game = new CardGame();
        $request = Request::create("test", "POST", ["type" => "DEAL_START"]);
        $game->processRequest($request); //Advances state to player turn
        $playerRequest = Request::create("test", "POST", ["type" => "PLAYER_DRAW"]);
        $messages = $game->processRequest($playerRequest);
        $this->assertEquals("PLAYER", $game->getState());
        $this->assertEquals($messages[0], "Processing user input...");
        $this->assertEquals($messages[1], "Drawing card...");
        //Draw many times to get fat
        for($i = 0; $i < 20; $i++) {
            $messages = $game->processRequest($playerRequest);
            if(count($messages) === 4 && $messages[3] === "You got fat") {
                break;
            }
        }
        if (count($messages) > 2)
        $this->assertStringContainsString("You drew", $messages[2]);
    }
    public function testProcessPlayerDrawWrongState(): void {
        $game = new CardGame();
        $playerRequest = Request::create("test", "POST", ["type" => "PLAYER_DRAW"]);
        $messages = $game->processRequest($playerRequest);
        $this->assertEquals("It is not the players turn, you cannot do this", $messages[0]);
    }
    public function testProcessPlayerLock(): void {
        $game = new CardGame();
        $request = Request::create("test", "POST", ["type" => "DEAL_START"]);
        $game->processRequest($request); //Advances state to player turn
        $playerRequest = Request::create("test", "POST", ["type" => "PLAYER_LOCK"]);
        $message = $game->processRequest($playerRequest);
        $this->assertEquals("Your cards are now locked in", $message[0]);
        $this->assertEquals("CPU", $game->getState());
        $errorMessage = $game->processRequest($playerRequest);
        $this->assertEquals("It is not the players turn, you cannot do this", $errorMessage[0]);
    }
    public function testSerialization(): void {
        $game = new CardGame();
        $game2 = unserialize(serialize($game));
        $this->assertEquals($game2, $game);
    }
}
