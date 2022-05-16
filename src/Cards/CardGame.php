<?php

namespace App\Cards;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
use App\Cards\Bank;
use App\Cards\PointSystem;
use App\Cards\CardGameState;
use Symfony\Component\HttpFoundation\Request;

/**
 * Card Game 21ish. handles requests and returns proper data for rendering
 */
class CardGame implements \Serializable
{
    private CardGameState $state;
    private TwigDeck $deck;
    private TwigPlayer $player;
    private Bank $cpu;

    public function __construct()
    {
        $this->player = new TwigPlayer();
        $this->cpu = new Bank();
        $this->deck = new TwigDeck(true);
        $this->state = new CardGameState();
        $this->deck->shuffleCards();
    }

    /**
     * Returns current state of the game
     * @return string
     */
    public function getState(): string
    {
        return $this->state->current();
    }

    /**
     * Processes the request from the user
     * @param Request $request
     *
     * @return array<string>
     */
    public function processRequest(Request $request): array
    {
        $type = $request->get("type");
        switch ($type) {
            case "DEAL_START":
                return $this->dealStarterCards();
            case "PROCESS_CPU":
                return $this->processCPU();
            case "PLAYER_LOCK":
                return $this->processPlayerLock();
            case "PLAYER_DRAW":
                return $this->processPlayerDraw();
            case "NEW_ROUND":
                return $this->newRound();
            default:
                return ["An unexpected error occurred"];
        }
    }

    /**
     * Deals starter cards
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function dealStarterCards(): array
    {
        if (!$this->state->is("NEW")) {
            return ["It is not the start of the game, you cannot do this"];
        }
        $this->deck->shuffleCards();
        $messages = ["Dealing starter cards"];
        array_push($messages, "Shuffling...");
        array_push($messages, "Dealing 1 card to you");
        array_push($messages, "Dealing 1 card to the bank");
        $this->player->addCards($this->deck->draw(1));
        $this->cpu->addCards($this->deck->draw(1));

        $this->state->advance();
        return $messages;
    }

    /**
     * Processes the cpus turn.
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function processCPU(): array
    {
        if (!$this->state->is("CPU")) {
            return ["It is not the banks turn, you cannot do this"];
        }
        $this->state->advance();
        return $this->cpu->processTurn($this->player, $this->deck);
    }

    /**
     * Locks in the players cards and advances state
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function processPlayerLock(): array
    {
        if (!$this->state->is("PLAYER")) {
            return ["It is not the players turn, you cannot do this"];
        }
        $messages = ["Your cards are now locked in"];
        $this->state->advance();
        return $messages;
    }

    /**
     * Clears the board, adds back the cards. Advances the state
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function newRound(): array
    {
        if (!$this->state->is("GAMEOVER")) {
            return ["It is not the end, you cannot do this"];
        }
        $messages = ["Starting new round"];
        $this->deck->addCards($this->player->clear());
        $this->deck->addCards($this->cpu->clear());
        $this->state->advance();
        return $messages;
    }

    /**
     * Draws a card for the player. Advances if player becomes fat.
     * Returns an array of strings documenting what happened.
     * @return array<string>
     */
    private function processPlayerDraw(): array
    {
        if (!$this->state->is("PLAYER")) {
            return ["It is not the players turn, you cannot do this"];
        }
        $messages = ["Processing user input..."];
        array_push($messages, "Drawing card...");
        $card = $this->deck->draw(1);
        array_push($messages, "You drew " . $card[0]->toString());
        $this->player->addCards($card);
        $bestPoint = PointSystem::bestPoint(PointSystem::points21($this->player->hand()));
        if (!$bestPoint) {
            array_push($messages, "You got fat");
            $this->state->set("GAMEOVER");
        } elseif ($bestPoint === 21) {
            array_push($messages, "BLACKJACK!");
            $this->state->advance();
        }
        return $messages;
    }

    /**
     * Returns a path to twig template based on the state
     * @return string
     */
    public function renderPath(): string
    {
        return $this->state->renderPath();
    }

    /**
     * @return array<string, mixed>
     */
    public function renderData(): array
    {
        if ($this->state->is("NEW")) {
            return $this->buildNewGameData();
        } elseif ($this->state->is("PLAYER")) {
            return $this->buildPlayerData();
        } elseif ($this->state->is("CPU")) {
            return $this->buildCPUData();
        } elseif ($this->state->is("GAMEOVER")) {
            return $this->buildResultData();
        }
        return [];
    }

    /**
     * Not yet implemented. Might not want to
     * @return array<string>
     */
    private function buildNewGameData(): array
    {
        return [];
    }

    /**
     * Returns array of information used in rendering
     * @return array<string, mixed>
     */
    private function buildPlayerData(): array
    {
        return [
            "player" => array_merge($this->player->twigArray(), ["points" => PointSystem::points21($this->player->hand())]),
            "cpu" => array_merge($this->cpu->twigArray(), ["points" => PointSystem::points21($this->cpu->hand())])
        ];
    }

    /**
     * Returns array of information used in rendering
     * @return array<string, mixed>
     */
    private function buildCPUData(): array
    {
        return $this->buildPlayerData();
    }

    /**
     * Returns array of information used in rendering
     * @return array<string, mixed>
     */
    private function buildResultData(): array
    {
        $resultMessage = "Bank wins";
        $cpuBestPoint = PointSystem::bestPoint(PointSystem::points21($this->cpu->hand()));
        $playerBestPoint = PointSystem::bestPoint(PointSystem::points21($this->player->hand()));
        if ($playerBestPoint > $cpuBestPoint) {
            $resultMessage = "Player wins";
        }
        return [
            "player" => array_merge($this->player->twigArray(), [
                    "points" => $playerBestPoint,
            ]),
            "cpu" => array_merge($this->cpu->twigArray(), [
                    "points" => $cpuBestPoint,
            ]),
            "resultMessage" => $resultMessage
        ];
    }

    /**
     * Serializes this object
     * @return string
     */
    public function serialize(): string
    {
        $data = [
            "player" => serialize($this->player),
            "cpu" => serialize($this->cpu),
            "deck" => serialize($this->deck),
            "state" => serialize($this->state)
        ];
        return serialize($data);
    }
    /**
     * Unserialize string, creates object
     * @param string $data
     *
     * @return void
     */
    public function unserialize(string $data): void
    {
        $data = unserialize($data);
        $this->player = unserialize($data["player"]);
        $this->cpu = unserialize($data["cpu"]);
        $this->deck = unserialize($data["deck"]);
        $this->state = unserialize($data["state"]);
    }
}
