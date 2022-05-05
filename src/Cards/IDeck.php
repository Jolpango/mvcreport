<?php

namespace App\Cards;

/**
 * Interface for Deck
 */
interface IDeck
{
    public function shuffleCards();
    public function draw(int $count);
    public function addCard(Card $card);
    public function addCards(array $card);
}
