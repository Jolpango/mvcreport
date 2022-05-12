<?php

namespace App\Cards;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
use App\Cards\PointSystem;
use Symfony\Component\HttpFoundation\Request;

/**
 * Card Game 21ish. handles requests and returns proper data for rendering
 */
class CardGame implements \Serializable
{
    /**
     * @var array<int, string>
     */
    public static array $States = [
        0 => "NEW",
        1 => "PLAYER",
        2 => "CPU",
        3 => "GAMEOVER"
    ];
    /**
     * @var array<int, string>
     */
    public static array $StateRenderPaths = [
        0 => "game/newgame.html.twig",
        1 => "game/player.html.twig",
        2 => "game/cpu.html.twig",
        3 => "game/result.html.twig"
    ];
    private int $state;
    private TwigDeck $deck;
    private TwigPlayer $player;
    private TwigPlayer $cpu;

    public function __construct()
    {
        $this->player = new TwigPlayer();
        $this->cpu = new TwigPlayer();
        $this->deck = new TwigDeck(true);
        $this->state = 0;
        $this->deck->shuffleCards();
    }

    /**
     * Advances the state of the game by 1
     * @return void
     */
    private function advanceState(): void
    {
        if ($this->state < 3) {
            $this->state++;
        } else {
            $this->state = 0;
        }
    }

    /**
     * Returns current state of the game
     * @return string
     */
    public function getState(): string
    {
        return self::$States[$this->state];
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
        $messages = [];
        switch ($type) {
            case "DEAL_START":
                $messages = $this->dealStarterCards();
                break;
            case "PROCESS_CPU":
                $messages = $this->processCPU();
                break;
            case "PLAYER_LOCK":
                $messages = $this->processPlayerLock();
                break;
            case "PLAYER_DRAW":
                $messages = $this->processPlayerDraw();
                break;
            case "NEW_ROUND":
                $messages = $this->newRound();
                break;
            default:
                $messages = ["An unexpected error occurred"];
                break;
        }
        return $messages;
    }

    /**
     * Deals starter cards
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function dealStarterCards(): array
    {
        if (CardGame::$States[$this->state] !== "NEW") {
            return ["It is not the start of the game, you cannot do this"];
        }
        $this->deck->shuffleCards();
        $messages = ["Dealing starter cards"];
        array_push($messages, "Shuffling...");
        array_push($messages, "Dealing 1 card to you");
        array_push($messages, "Dealing 1 card to the bank");
        $this->player->addCards($this->deck->draw(1));
        $this->cpu->addCards($this->deck->draw(1));

        $this->advanceState();
        return $messages;
    }

    /**
     * Processes the cpus turn.
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function processCPU(): array
    {
        if (CardGame::$States[$this->state] !== "CPU") {
            return ["It is not the banks turn, you cannot do this"];
        }
        $messages = ["Computer/Developer is thinking..."];
        while ($this->shouldCPUDraw()) {
            $card = $this->deck->draw(1);
            array_push($messages, "Computer drew " . $card[0]->toString());
            $this->cpu->addCards($card);
        }
        array_push($messages, "Computer has finished thinking");
        $this->advanceState();
        return $messages;
    }

    /**
     * Calculates whether or not the cpu should draw a card or settle
     * @return bool
     */
    private function shouldCPUDraw(): bool
    {
        $cpuPoints = PointSystem::points21($this->cpu->hand());
        $playerPoints = PointSystem::points21($this->player->hand());
        $playerBestPoint = PointSystem::bestPoint($playerPoints);
        $cpuBestPoint = PointSystem::bestPoint($cpuPoints);
        // cpu is fat
        if (!$cpuBestPoint || !$playerBestPoint) {
            return false;
        }
        if ($cpuBestPoint >= $playerBestPoint) {
            return false;
        }
        return true;
    }

    /**
     * Locks in the players cards and advances state
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function processPlayerLock(): array
    {
        if (CardGame::$States[$this->state] !== "PLAYER") {
            return ["It is not the players turn, you cannot do this"];
        }
        $messages = ["Your cards are now locked in"];
        $this->advanceState();
        return $messages;
    }

    /**
     * Clears the board, adds back the cards. Advances the state
     * Returns an array of strings documenting what happened
     * @return array<string>
     */
    private function newRound(): array
    {
        if (CardGame::$States[$this->state] !== "GAMEOVER") {
            return ["It is not the end, you cannot do this"];
        }
        $messages = ["Starting new round"];
        $this->deck->addCards($this->player->clear());
        $this->deck->addCards($this->cpu->clear());
        $this->advanceState();
        return $messages;
    }

    /**
     * Draws a card for the player. Advances if player becomes fat.
     * Returns an array of strings documenting what happened.
     * @return array<string>
     */
    private function processPlayerDraw(): array
    {
        if (CardGame::$States[$this->state] !== "PLAYER") {
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
            $this->state = 3;
        } elseif ($bestPoint === 21) {
            array_push($messages, "BLACKJACK!");
            $this->advanceState();
        }
        return $messages;
    }

    /**
     * Returns a path to twig template based on the state
     * @return string
     */
    public function renderPath(): string
    {
        return CardGame::$StateRenderPaths[$this->state];
    }

    /**
     * @return array<string, mixed>
     */
    public function renderData(): array
    {
        if (CardGame::$States[$this->state] === "NEW") {
            return $this->buildNewGameData();
        } elseif (CardGame::$States[$this->state] === "PLAYER") {
            return $this->buildPlayerData();
        } elseif (CardGame::$States[$this->state] === "CPU") {
            return $this->buildCPUData();
        } elseif (CardGame::$States[$this->state] === "GAMEOVER") {
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
        $gameData = [];

        return $gameData;
    }

    /**
     * Returns array of information used in rendering
     * @return array<string, mixed>
     */
    private function buildPlayerData(): array
    {
        $gameData = [
            "player" => array_merge($this->player->twigArray(), ["points" => PointSystem::points21($this->player->hand())]),
            "cpu" => array_merge($this->cpu->twigArray(), ["points" => PointSystem::points21($this->cpu->hand())])
        ];
        return $gameData;
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
        $gameData = [
            "player" => array_merge($this->player->twigArray(), [
                    "points" => $playerBestPoint,
            ]),
            "cpu" => array_merge($this->cpu->twigArray(), [
                    "points" => $cpuBestPoint,
            ]),
            "resultMessage" => $resultMessage
        ];
        return $gameData;
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
