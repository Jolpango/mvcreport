<?php

namespace App\Cards;

interface IDeck
{
    public function shuffle();
    public function draw(int $count);
    public function addCard(Card $card);
    public function addCards(array $card);
}
