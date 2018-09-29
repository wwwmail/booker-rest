<?php

namespace tests;

use Application\Entity\Rooms;
use Application\Entity\Base;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class RoomsTest extends TestCase {

    private $rooms;

    protected function setUp()
    {
        $this->rooms = new Rooms();
    }

    protected function tearDown()
    {
        $this->rooms = NULL;
    }

    public function testSetName()
    {
        $result = $this->rooms->setName('test');
        $this->assertEquals('test', $this->rooms->getName());
    }

    public function testAttributeMapping()
    {
        $this->assertClassHasAttribute('mapping', 'Application\Entity\Rooms');
    }

}
