<?php

namespace App\Cards;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Player
 */
class PlayerTest extends TestCase
{
    /**
     * Test creating player
     * @return void
     */
    public function testCreationNoArgs(): void {
        $p = new Player();
        $this->assertInstanceOf("\App\Cards\Player", $p);
        $this->assertEquals(["name" => "NoName", "hand" => []], $p->toArray());
    }
}
