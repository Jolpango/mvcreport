<?php

namespace App\Cards;

use App\Cards\TwigDeck;
use App\Cards\TwigPlayer;
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
    private TwigDeck $discardPile;

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
                $messages =  $this->processCPU();
                break;
            case "PLAYER":
                $messages =  $this->processPlayerRequest($request);
                break;
            default:
                $messages =  ["An unexpected error occurred"];
                break;
        }
        return $messages;
    }

    public function dealStarterCards(): array {
        if (CardGame::$States[$this->state] !== "NEW") {
            return ["It is not the start of the game, you cannot do this"];
        }
        $messages = ["Dealing starter cards"];
        array_push($messages, "Dealing 2 cards to you");
        array_push($messages, "Dealing 2 cards to the bank");
        $this->player->addCards($this->deck->draw(2));
        $this->cpu->addCards($this->deck->draw(2));

        $this->advanceState();
        return $messages;
    }

    public function processCPU(): array {
        if (CardGame::$States[$this->state] !== "CPU") {
            return ["It is not the banks turn, you cannot do this"];
        }
        $messages = ["Computer is thinking..."];

        return $messages;
    }

    public function processPlayerRequest(Request $request): array {
        if (CardGame::$States[$this->state] !== "PLAYER") {
            return ["It is not the players turn, you cannot do this"];
        }
        $messages = ["Processing user input"];

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
            "player" => $this->player->twigArray(),
            "cpu" => $this->cpu->twigArray()
        ];
        return $gameData;
    }

    private function buildCPUData(): array {
        return buildPlayerData();
    }

    private function buildResultData(): array {
        $gameData = [];

        return $gameData;
    }

    public function serialize() {
        $data = [
            "player" => serialize($this->player),
            "cpu" => serialize($this->cpu),
            "deck" => serialize($this->deck),
            "discardPile" => serialize($this->discardPile) ,
            "state" => serialize($this->state)
        ];
        return serialize($data);
    }
    public function unserialize($data) {
        $data = unserialize($data);
        $this->player = unserialize($data["player"]);
        $this->cpu = unserialize($data["cpu"]);
        $this->deck = unserialize($data["deck"]);
        $this->discardPile = unserialize($data["discardPile"]);
        $this->state = unserialize($data["state"]);
    }
}
