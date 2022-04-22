<?php

namespace App\Cards;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
use App\Cards\PointSystem;
use Symfony\Component\HttpFoundation\Request;

class CardGame implements \Serializable
{
    public static array $States = [
        0 => "NEW",
        1 => "PLAYER",
        2 => "CPU",
        3 => "GAMEOVER"
    ];
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

    public function __construct($nrOfDecks=2)
    {
        $this->player = new TwigPlayer();
        $this->cpu = new TwigPlayer();
        $this->deck = new TwigDeck(true);
        $this->discardPile = new TwigDeck();
        $this->state = 0;
        $this->deck->shuffleCards();
    }

    private function advanceState(): void {
        if ($this->state < 3) {
            $this->state++;
        } else {
            $this->state = 0;
        }
    }

    public function processRequest(Request $request): array {
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

    public function dealStarterCards(): array {
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

    public function processCPU(): array {
        if (CardGame::$States[$this->state] !== "CPU") {
            return ["It is not the banks turn, you cannot do this"];
        }
        $messages = ["Computer/Developer is thinking..."];
        $pointsArray = PointSystem::Points21($this->cpu->hand());
        $playerPoints = PointSystem::Points21($this->player->hand());
        while($this->shouldCPUDraw()) {
            $card = $this->deck->draw(1);
            array_push($messages, "Computer drew " . $card[0]->toString());
            $this->cpu->addCards($card);
            $pointsArray = PointSystem::Points21($this->cpu->hand());
        }
        array_push($messages, "Computer has finished thinking");
        $this->advanceState();
        return $messages;
    }

    private function shouldCPUDraw(): bool {
        $cpuPoints = PointSystem::Points21($this->cpu->hand());
        $playerPoints = PointSystem::Points21($this->player->hand());
        $playerBestPoint = PointSystem::BestPoint($playerPoints);
        $cpuBestPoint = PointSystem::BestPoint($cpuPoints);
        $cutOffPoint = 17;
        // cpu is fat
        if (!$cpuBestPoint || !$playerBestPoint) {
            return false;
        }
        if ($cpuBestPoint >= $playerBestPoint) {
            return false;
        }
        return true;
    }

    public function processPlayerLock(): array {
        if (CardGame::$States[$this->state] !== "PLAYER") {
            return ["It is not the players turn, you cannot do this"];
        }
        $messages = ["Your cards are now locked in"];
        $this->advanceState();
        return $messages;
    }

    public function newRound(): array {
        if (CardGame::$States[$this->state] !== "GAMEOVER") {
            return ["It is not the end, you cannot do this"];
        }
        $messages = ["Starting new round"];
        $this->deck->addCards($this->player->clear());
        $this->deck->addCards($this->cpu->clear());
        $this->advanceState();
        return $messages;
    }

    public function processPlayerDraw(): array {
        if (CardGame::$States[$this->state] !== "PLAYER") {
            return ["It is not the players turn, you cannot do this"];
        }
        $messages = ["Processing user input..."];
        array_push($messages, "Drawing card...");
        $card = $this->deck->draw(1);
        array_push($messages, "You drew " . $card[0]->toString());
        $this->player->addCards($card);
        $bestPoint = PointSystem::BestPoint(PointSystem::Points21($this->player->hand()));
        if (!$bestPoint) {
            array_push($messages, "You got fat");
            $this->state = 3;
        } else if ($bestPoint === 21) {
            array_push($messages, "BLACKJACK!");
            $this->advanceState();
        }
        return $messages;
    }

    public function renderPath(): string {
        return CardGame::$StateRenderPaths[$this->state];
    }

    public function renderData(): array {
        if (CardGame::$States[$this->state] === "NEW") {
            return $this->buildNewGameData();
        } else if (CardGame::$States[$this->state] === "PLAYER") {
            return $this->buildPlayerData();
        } else if (CardGame::$States[$this->state] === "CPU") {
            return $this->buildCPUData();
        } else if (CardGame::$States[$this->state] === "GAMEOVER") {
            return $this->buildResultData();
        }
    }

    private function buildNewGameData(): array {
        $gameData = [];

        return $gameData;
    }

    private function buildPlayerData(): array {
        $gameData = [
            "player" => array_merge($this->player->twigArray(), ["points" => PointSystem::Points21($this->player->hand())]),
            "cpu" => array_merge($this->cpu->twigArray(), ["points" => PointSystem::Points21($this->cpu->hand())])
        ];
        return $gameData;
    }

    private function buildCPUData(): array {
        return $this->buildPlayerData();
    }

    private function buildResultData(): array {
        $resultMessage = "Bank wins";
        $cpuBestPoint = PointSystem::BestPoint(PointSystem::Points21($this->cpu->hand()));
        $playerBestPoint = PointSystem::BestPoint(PointSystem::Points21($this->player->hand()));
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

    public function serialize() {
        $data = [
            "player" => serialize($this->player),
            "cpu" => serialize($this->cpu),
            "deck" => serialize($this->deck),
            "state" => serialize($this->state)
        ];
        return serialize($data);
    }
    public function unserialize($data) {
        $data = unserialize($data);
        $this->player = unserialize($data["player"]);
        $this->cpu = unserialize($data["cpu"]);
        $this->deck = unserialize($data["deck"]);
        $this->state = unserialize($data["state"]);
    }
}
