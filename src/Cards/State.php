<?php

namespace App\Cards;

/**
 * Class State
 */
class State implements \Serializable
{
    protected $states = [];
    protected int $currentState = 0;
    /**
     * @param array $states
     * @param int $start=0
     */
    public function __construct(array $states, int $start=0)
    {
        $this->states = $states;
        $this->currentState = $start;
    }
    /**
     * @param string $state
     *
     * @return bool
     */
    public function is(string $state): bool
    {
        return $state === $this->current();
    }
    /**
     * Returns current state
     * @return string
     */
    public function current(): string
    {
        return $this->states[$this->currentState];
    }
    /**
     * Sets state, throws exception if state does not exist.
     * @param string $newState
     *
     * @return void
     */
    public function set(string $newState): void
    {
        $index = array_search($newState, $this->states);
        if (!$index) {
            throw new \Exception("State not found", 1);
        }
        $this->currentState = $index;
    }
    /**
     * Advances state by 1. If at max length, starts over.
     * @return void
     */
    public function advance(): void
    {
        $this->currentState = ($this->currentState + 1) % count($this->states);
    }
    /**
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            "current" => serialize($this->currentState),
            "states" => serialize($this->states)
        ]);
    }
    /**
     * @param string $data
     *
     * @return void
     */
    public function unserialize(string $data): void
    {
        $data = unserialize($data);
        $this->currentState = unserialize($data["current"]);
        $this->states = unserialize($data["states"]);
    }
}
