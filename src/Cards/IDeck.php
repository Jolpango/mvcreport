<?php

namespace App\Cards;

interface IDeck
{
    public function shuffleCards();
    public function draw(int $count);
    public function addCard(Card $card);
    public function addCards(array $card);
}
