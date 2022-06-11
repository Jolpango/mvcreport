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
    private int $bankRaise;

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
        $this->bankRaise = 0;
    }

    /**
     * Sets up the states
     * @return void
     */
    private function setupStates(): void
    {
        $this->state = new State([
            "BLIND",
            "FLOP",
            "TURN",
            "RESPONSE",
            "RIVER",
            "TURN",
            "RESPONSE",
            "RIVER",
            "TURN",
            "RESPONSE",
            "END"
        ]);
    }

    /**
     * Sets up the rules
     * @return void
     */
    private function setupRules(): void
    {
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
    public function getSmallBlind(): int
    {
        return $this->smallBlind;
    }
    /**
     * @return int
     */
    public function getBankRaise(): int
    {
        return $this->bankRaise;
    }
    /**
     * @return int
     */
    public function getBigBlind(): int
    {
        return $this->bigBlind;
    }
    /**
     * @return TwigPlayer
     */
    public function getBank(): TwigPlayer
    {
        return $this->bank;
    }
    /**
     * @return TwigPlayer
     */
    public function getPlayer(): TwigPlayer
    {
        return $this->player;
    }
    /**
     * @return TwigPlayer
     */
    public function getTable(): TwigPlayer
    {
        return $this->table;
    }
    public function getPot(): int
    {
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
                if (!$this->state->is("BLIND")) {
                    return ["Wrong state"];
                }
                return $this->blind($user);
            case "DEAL_FLOP":
                if (!$this->state->is("FLOP")) {
                    return ["Wrong state"];
                }
                return $this->flop();
            case "BET":
                if (!$this->state->is("TURN")) {
                    return ["Wrong state"];
                }
                return $this->bet($request, $user);
            case "CHECK":
                if (!$this->state->is("TURN")) {
                    return ["Wrong state"];
                }
                return $this->check($user);
            case "FOLD":
                if (!$this->state->is("TURN") || !$this->state->is("RESPONSE")) {
                    return ["Wrong state"];
                }
                return $this->fold();
            case "CALL":
                if (!$this->state->is("RESPONSE")) {
                    return ["Wrong state"];
                }
                return $this->call($user);
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
    private function blind(User $user): array
    {
        if ($user->getBalance() < $this->smallBlind) {
            return ["Not enough coins"];
        }
        $messages = ["Adding blinds"];
        $user->setBalance($user->getBalance() - $this->smallBlind);
        $this->pot += $this->smallBlind * 2;
        $this->state->advance();
        return $messages;
    }

    /**
     * @return array<string>
     */
    private function flop(): array
    {
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
    private function bet(Request $request, User $user): array
    {
        if ($request->get("amount") > $user->getBalance()) {
            return ["You dont have enough coins"];
        }
        $messages = ["Processing bet"];
        $this->pot += $request->get("amount");
        $user->setBalance($user->getBalance() - $request->get("amount"));
        // Determine if call of not
        $messages = array_merge($messages, $this->bank($user, $request->get("amount")));
        return $messages;
    }


    private function handWorth($cards, $opponentTotal, $opponentRaise): int
    {
        // $cardsLeft = 7 - count($cards);
        $confidenceCoefficient = 2;
        $points = false;
        for ($i = 0; $i < count($this->rules) && !$points; $i++) {
            $points = $this->rules[$i]->calculate($cards);
        }
        $potential = ($points->getPoint() * ($opponentTotal / 10) - ($opponentRaise / 2)) * $confidenceCoefficient;
        if ($potential > $opponentTotal) {
            return $opponentTotal;
        }
        return $potential;
    }

    /**
     * @param User $user
     * @param int $amount
     *
     * @return array<string>
     */
    private function bank(User $user, int $amount): array
    {
        $messages = ["Processing..."];
        $callCoefficient = 0.75;
        $raiseCoefficient = 2;
        $recklessCoefficient = 20;
        $bluffCallCoefficient = 50;
        $reckless = rand(1, 100) < $recklessCoefficient;
        $callBluff = rand(1, 100) < $bluffCallCoefficient;
        if ($amount === 0) {
            $worth = $this->handWorth(array_merge($this->bank->hand(), $this->table->hand()), $user->getBalance(), $amount);
            if (($worth >= $raiseCoefficient * $user->getBalance() / 10 || $reckless) && $user->getBalance() !== 0) {
                $this->bankRaise = $worth;
                array_push($messages, "The bank raises by " . $this->bankRaise);
                $this->pot += $this->bankRaise;
                $this->state->advance();
            } else {
                array_push($messages, "The bank checks");
                $this->state->advance();
                $this->state->advance();
                if ($this->state->is("RIVER")) {
                    $messages = array_merge($messages, $this->river());
                } elseif ($this->state->is("END")) {
                    $messages = array_merge($messages, $this->end($user));
                }
            }
        } else {
            $worth = $this->handWorth(array_merge($this->bank->hand(), $this->table->hand()), $user->getBalance(), $amount);
            if ($worth > $amount * $raiseCoefficient && $user->getBalance() !== 0) {
                $this->bankRaise = $worth - $amount;
                array_push($messages, "The bank raises by " . $this->bankRaise);
                $this->pot += $amount + $this->bankRaise;
                $this->state->advance();
            } elseif ($worth >= $amount * $callCoefficient || $callBluff) {
                array_push($messages, "The bank calls");
                $this->pot += $amount;
                $this->state->advance();
                $this->state->advance();
                if ($this->state->is("RIVER")) {
                    $messages = array_merge($messages, $this->river());
                } elseif ($this->state->is("END")) {
                    $messages = array_merge($messages, $this->end($user));
                }
            } else {
                array_push($messages, "The bank folds");
                $this->bank->fold();
                $this->state->set("END");
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
    private function call(User $user): array
    {
        $messages = ["Calling"];
        $this->state->advance();
        $user->setBalance($user->getBalance() - $this->bankRaise);
        $this->pot += $this->bankRaise;
        $this->bankRaise = 0;
        if ($this->state->is("RIVER")) {
            $messages = array_merge($messages, $this->river());
        } elseif ($this->state->is("END")) {
            $messages = array_merge($messages, $this->end($user));
        }
        return $messages;
    }

    /**
     * @param User $user
     *
     * @return array<string>
     */
    private function check(User $user): array
    {
        $messages = ["Checking"];
        $messages = array_merge($messages, $this->bank($user, 0));
        return $messages;
    }

    /**
     *
     * @return array<string>
     */
    private function fold(): array
    {
        $messages = ["You fold"];
        $this->player->fold();
        $this->state->set("END");
        return $messages;
    }

    /**
     * Performs end calculation
     * @param User $user
     *
     * @return array<string>
     */
    private function end(User $user): array
    {
        if ($this->player->getFolded()) {
            return ["Bank wins"];
        }
        if ($this->bank->getFolded()) {
            $user->setBalance($user->getBalance() + $this->pot);
            return ["Player wins"];
        }
        $messages = ["Calculating"];
        $count = count($this->rules);
        $playerPoint = false;
        $bankPoint = false;
        for ($i = 0; $i < $count && (!$playerPoint || !$bankPoint); $i++) {
            if (!$playerPoint) {
                $playerPoint = $this->rules[$i]->calculate(array_merge($this->player->hand(), $this->table->hand()));
            }
            if (!$bankPoint) {
                $bankPoint = $this->rules[$i]->calculate(array_merge($this->bank->hand(), $this->table->hand()));
            }
        }
        // Assume points became Point objects
        if ($playerPoint->getPoint() > $bankPoint->getPoint()) {
            array_push($messages, "Player wins with a " . $playerPoint->getMessage());
            $user->setBalance($user->getBalance() + $this->pot);
        } elseif ($playerPoint->getPoint() < $bankPoint->getPoint()) {
            array_push($messages, "Bank wins with a " . $bankPoint->getMessage());
        } else {
            if ($playerPoint->getTieBreakerPoint() > $bankPoint->getTieBreakerPoint()) {
                array_push($messages, "Player wins with a " . $playerPoint->getMessage());
                $user->setBalance($user->getBalance() + $this->pot);
            } elseif ($playerPoint->getTieBreakerPoint() < $bankPoint->getTieBreakerPoint()) {
                array_push($messages, "Bank wins with a " . $bankPoint->getMessage());
            } else {
                $highHand = new HighHand();
                $playerHighestCardPoint = $highHand->calculate($this->player->hand());
                $bankHighestCardPoint = $highHand->calculate($this->bank->hand());
                if ($playerHighestCardPoint->getTieBreakerPoint() > $bankHighestCardPoint->getTieBreakerPoint()) {
                    array_push($messages, "Player wins on a tie with the highest card");
                    $user->setBalance($user->getBalance() + $this->pot);
                } elseif ($playerHighestCardPoint->getTieBreakerPoint() < $bankHighestCardPoint->getTieBreakerPoint()) {
                    array_push($messages, "Bank wins on a tie with the highest card");
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
    private function reset(): array
    {
        $this->player->clear();
        $this->bank->clear();
        $this->table->clear();
        $this->deck = new TwigDeck(true);
        $this->deck->shuffleCards();
        $this->pot = 0;
        $this->bankRaise = 0;
        $this->state->set("BLIND");
        return ["Reshuffling"];
    }

    /**
     * @return array<string>
     */
    private function river(): array
    {
        $messages = ["Dealing river card"];
        $this->table->addCards($this->deck->draw(1));
        $this->state->advance();
        return $messages;
    }

    public function renderPath(): string
    {
        return "poker/game/" . strtolower($this->state->current()) . ".html.twig";
    }
}
