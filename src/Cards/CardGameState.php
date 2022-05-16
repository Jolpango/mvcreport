<?php

namespace App\Cards;

use App\Cards\State;

/**
 * Class CardGameState
 */
class CardGameState extends State
{
    private $renderPaths = [
        "game/newgame.html.twig",
        "game/player.html.twig",
        "game/cpu.html.twig",
        "game/result.html.twig"
    ];
    public function __construct() {
        parent::__construct(
            ["NEW", "PLAYER", "CPU", "GAMEOVER"]
        );
    }
    /**
     * @return string
     */
    public function renderPath(): string {
        return $this->renderPaths[$this->currentState];
    }
        /**
     * @return string
     */
    public function serialize(): string {
        return serialize([
            "current" => serialize($this->currentState),
            "states" => serialize($this->states),
            "paths" => serialize($this->renderPaths)
        ]);
    }
    /**
     * @param string $data
     * 
     * @return void
     */
    public function unserialize(string $data): void {
        $data = unserialize($data);
        $this->currentState = unserialize($data["current"]);
        $this->states = unserialize($data["states"]);
        $this->renderPaths = unserialize($data["paths"]);
    }
}
