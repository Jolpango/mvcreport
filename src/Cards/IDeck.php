<?php

namespace App\Cards;

/**
 * Interface for Deck
 */
interface IDeck
{
    /**
     * @return bool
     */
    public function shuffleCards();
    /**
     * @param int $count
     * 
     * @return array<Card>
     */
    public function draw(int $count);
    /**
     * @param Card $card
     * 
     * @return void
     */
    public function addCard(Card $card);
    /**
     * @param array<Card> $card
     * 
     * @return void
     */
    public function addCards(array $card);
}
