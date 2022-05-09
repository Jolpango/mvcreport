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
    /**
     * @return void
     */
    public function testClear(): void {
        $p = new Player([1, 2, 3]);
        $p->clear();
        $this->assertEquals([], $p->hand());
    }
    /**
     * @return void
     */
    public function testSerialization(): void {
        $p = new Player([1, 2, 3], "John");
        $pString = serialize($p);
        $p2 = unserialize($pString);
        $this->assertEquals($p, $p2);
    }
    /**
     * @return void
     */
    public function testAddCards(): void {
        $p = new Player();
        $p->addCards([new Card(1, "Hearts")]);
        $p->addCards([new Card(2, "Hearts")]);
        $this->assertEquals($p->hand(), [new Card(1, "Hearts"), new Card(2, "Hearts")]);
    }
    public function testCount(): void {
        $p = new Player();
        $p->addCards([1, 2]);
        $this->assertEquals(count($p), 2);
        $p->addCards([2, 3]);
        $this->assertEquals(count($p), 4);
    }
    public function testToArray(): void {
        $p = new Player();
        $this->assertInstanceOf("\App\Cards\Player", $p);
        $p->addCards([new Card(1, "Hearts")]);
        $p->addCards([new Card(2, "Hearts")]);
        $this->assertEquals(["name" => "NoName", "hand" => [["value" => 1, "suit" => "Hearts"], ["value" => 2, "suit" => "Hearts"]]], $p->toArray());
    }
}
