<?php

namespace App\Poker;

use App\Cards\TwigPlayer;
use App\Cards\TwigDeck;
use App\Cards\State;
use App\Poker\Bank;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

/**
 * Card Game 21ish. handles requests and returns proper data for rendering
 */
class PokerGame
{
    private State $state;
    private int $pot;
    private TwigPlayer $player;
    private TwigPlayer $table;
    private Bank $bank;
    private TwigDeck $deck;
    private int $smallBlind;
    private int $bigBlind;

    public function __construct()
    {
        $this->state = new State([
            "BLIND",
            "FLOP",
            "TURN",
            "RIVER",
            "TURN",
            "RIVER",
            "TURN",
            "END"
        ]);
        $this->player = new TwigPlayer();
        $this->bank = new Bank();
        $this->deck = new TwigDeck(true);
        $this->deck->shuffleCards();
        $this->table = new TwigPlayer();
        $this->smallBlind = 100;
        $this->bigBlind = 200;
        $this->pot = 0;
    }

    /**
     * @return int
     */
    public function getSmallBlind(): int {
        return $this->smallBlind;
    }
    /**
     * @return int
     */
    public function getBigBlind(): int {
        return $this->bigBlind;
    }
    /**
     * @return TwigPlayer
     */
    public function getBank(): TwigPlayer {
        return $this->bank;
    }
    /**
     * @return TwigPlayer
     */
    public function getPlayer(): TwigPlayer {
        return $this->player;
    }
    /**
     * @return TwigPlayer
     */
    public function getTable(): TwigPlayer {
        return $this->table;
    }
    public function getPot(): int {
        return $this->pot;
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
    public function processRequest(Request $request, User $user): array
    {
        $type = $request->get("type");
        switch ($type) {
            case "BLIND":
                return $this->blind($user);
            case "DEAL_FLOP":
                return $this->flop();
            case "BET":
                return $this->bet($request, $user);
            case "CHECK":
                return $this->check($user);
            default:
                return ["An unexpected error occurred"];
        }
    }

    /**
     * @param User $user
     * 
     * @return array<string>
     */
    private function blind(User $user): array {
        $messages = ["Adding blinds"];
        $user->setBalance($user->getBalance() - $this->smallBlind);
        $this->pot += $this->smallBlind * 2;
        $this->state->advance();
        return $messages;
    }

    /**
     * @return array<string>
     */
    private function flop(): array {
        $messages = ["Dealing flop"];
        $this->player->addCards($this->deck->draw(2));
        $this->table->addCards($this->deck->draw(3));
        $this->bank->addCards($this->deck->draw(2));
        $this->state->advance();
        return $messages;
    }

    /**
     * @param Request $request
     * @param User $user
     * 
     * @return array<string>
     */
    private function bet(Request $request, User $user): array {
        if ($request->get("amount") > $user->getBalance()) {
            return ["You dont have enough coinds"];
        }
        $messages = ["Processing bet"];
        $this->pot += $request->get("amount");
        $user->setBalance($user->getBalance() - $request->get("amount"));
        // Determine if call of not
        if ($request->get("amount") < 1000000000) {
            array_push($messages, "The bank calls");
            $this->pot += $request->get("amount");
            $this->state->advance();
            if ($this->state->is("RIVER")) {
                array_merge($messages, $this->river());
            }
        }
        return $messages;
    }
    /**
     * @param User $user
     * 
     * @return array<string>
     */
    private function check(User $user): array {
        $messages = ["Checking"];
        $this->state->advance();
        // Can bank bet?
        if ($this->state->is("RIVER")) {
            array_merge($messages, $this->river());
        }
        return $messages;
    }

    /**
     * @return array<string>
     */
    private function river(): array {
        $messages = ["Dealing river card"];
        $this->table->addCards($this->deck->draw(1));
        $this->state->advance();
        return $messages;
    }

    public function renderPath(): string {
        return "poker/game/" . strtolower($this->state->current()) . ".html.twig";
    }
}
