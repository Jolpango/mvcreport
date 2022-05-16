<?php

namespace App\Cards;

use App\Cards\TwigPlayer;
use App\Cards\Player;
use App\Cards\Deck;

/**
 * Class Bank
 */
class Bank extends TwigPlayer
{
    /**
     * @param Player $opponent
     * @param Deck $deck
     * 
     * @return array
     */
    public function processTurn(Player $opponent, Deck $deck): array {
        $messages = ["Computer/Developer is thinking..."];
        while ($this->shouldDraw($opponent)) {
            $card = $deck->draw(1);
            array_push($messages, "Computer drew " . $card[0]->toString());
            $this->addCards($card);
        }
        array_push($messages, "Computer has finished thinking");
        return $messages;
    }
    /**
     * Calculates whether or not the cpu should draw a card or settle
     * @return bool
     */
    private function shouldDraw(Player $opponent): bool {
        $cpuPoints = PointSystem::points21($this->hand);
        $playerPoints = PointSystem::points21($opponent->hand());
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
}
