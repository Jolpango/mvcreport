<?php

namespace App\Poker;

use App\Cards\Player;
use App\Cards\TwigPlayer;
use App\Cards\TwigDeck;
use App\Cards\State;
use App\Poker\Bank;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Poker\Rules\FourOfAKind;
use App\Poker\Rules\FullHouse;
use App\Poker\Rules\HighHand;
use App\Poker\Rules\RoyalFlush;
use App\Poker\Rules\Straight;
use App\Poker\Rules\StraightFlush;
use App\Poker\Rules\ThreeOfAKind;
use App\Poker\Rules\TwoPair;
use App\Poker\Rules\Pair;
use App\Poker\Rules\Flush;
use App\Poker\Rules\Point;

/**
 * Card Game 21ish. handles requests and returns proper data for rendering
 */
class PokerGame
{
    private array $rules;
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
        $this->setupStates();
        $this->setupRules();
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
     * Sets up the states
     * @return void
     */
    private function setupStates(): void {
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
    }

    /**
     * Sets up the rules
     * @return void
     */
    private function setupRules(): void {
        $this->rules = [
            new RoyalFlush(),
            new StraightFlush(),
            new FourOfAKind(),
            new FullHouse(),
            new Flush(),
            new Straight(),
            new ThreeOfAKind(),
            new TwoPair(),
            new Pair(),
            new HighHand()
        ];
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
            case "FOLD":
                return $this->fold();
            case "RESET":
                return $this->reset();
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
                $messages = array_merge($messages, $this->river());
            } elseif ($this->state->is("END")) {
                $messages = array_merge($messages, $this->end($user));
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
            $messages = array_merge($messages, $this->river());
        } elseif ($this->state->is("END")) {
            $messages = array_merge($messages, $this->end($user));
        }
        return $messages;
    }

    /**
     * 
     * @return array<string>
     */
    private function fold(): array {
        $messages = ["You fold"];
        $this->state->set("END");
        return $messages;
    }

    /**
     * Performs end calculation
     * @param User $user
     * 
     * @return array<string>
     */
    private function end(User $user): array {
        $messages = ["Calculating"];
        $count = count($this->rules);
        $playerPoint = false;
        $bankPoint = false;
        for ($i = 0; $i < $count && !$playerPoint && !$bankPoint; $i++) {
            if (!$playerPoint) {
                $playerPoint = $this->rules[$i]->calculate(array_merge($this->player->hand(), $this->table->hand()));
            }
            if (!$bankPoint) {
                $bankPoint = $this->rules[$i]->calculate(array_merge($this->bank->hand(), $this->table->hand()));
            }
        }
        // Assume points became Point objects
        if ($playerPoint->getPoint() > $bankPoint->getPoint()) {
            array_push($messages, "Player wins");
            $user->setBalance($user->getBalance() + $this->pot);
        } elseif ($playerPoint->getPoint() < $bankPoint->getPoint()) {
            array_push($messages, "Bank wins");
        } else {
            if ($playerPoint->getTieBreakerPoint() > $bankPoint->getTieBreakerPoint()) {
                array_push($messages, "Player wins");
                $user->setBalance($user->getBalance() + $this->pot);
            } elseif ($playerPoint->getTieBreakerPoint() < $bankPoint->getTieBreakerPoint()) {
                array_push($messages, "Bank wins");
            } else {
                $highHand = new HighHand();
                $playerHighestCardPoint = $highHand->calculate($this->player->hand());
                $bankHighestCardPoint = $highHand->calculate($this->bank->hand());
                if ($playerHighestCardPoint->getTieBreakerPoint() > $bankHighestCardPoint->getTieBreakerPoint()) {
                    array_push($messages, "Player wins");
                    $user->setBalance($user->getBalance() + $this->pot);
                } elseif ($playerHighestCardPoint->getTieBreakerPoint() < $bankHighestCardPoint->getTieBreakerPoint()) {
                    array_push($messages, "Bank wins");
                } else {
                    array_push($messages, "It's a tie");
                    $user->setBalance($user->getBalance() + ($this->pot / 2));
                }
            }
        }
        return $messages;
    }

    /**
     * 
     * @return array<string>
     */
    private function reset(): array {
        $this->player->clear();
        $this->bank->clear();
        $this->table->clear();
        $this->deck = new TwigDeck(true);
        $this->deck->shuffleCards();
        $this->pot = 0;
        $this->state->set("BLIND");
        return ["Reshuffling"];
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
