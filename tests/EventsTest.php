<?php
namespace tests;

use Application\Entity\Events;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;



class EventsTest extends TestCase
{
    private $events;
 
    protected function setUp()
    {
        $this->events = new Events();
    }

    protected function tearDown()
    {
        $this->events = NULL;
    }

    public function testSetRecursion()
    {
        $result = $this->events->setRecursion(1);
        $this->assertEquals(1, $this->events->getRecursion());
    }
    
    public function testTypeRecursion()
    {
        $result = $this->events->setRecursion('1');
      
        $this->assertInternalType('int', $this->events->getRecursion()); 
    }
    
    public function testSetRecursionId()
    {
        $result = $this->events->setRecursionId('200');
      
        $this->assertInternalType('int', $this->events->getRecursionId()); 
    }
    
    public function testSetUserId()
    {
        $result = $this->events->setUserId(3);
         $this->assertEquals(3,  $this->events->getUserId());
    }
    
    public function testTypeSetUserId()
    {
        $result = $this->events->setUserId(3);
        $this->assertInternalType('int', $this->events->getUserId()); 
    }
    
    
    public function testSetDescription()
    {
        $result = $this->events->setDescription('some text');
        $this->assertEquals('some text', $this->events->getDescription()); 
        
    }

    

    public function testTypeSetDescription()
    {
        $result = $this->events->setDescription(3333);
        $this->assertInternalType('string', $this->events->getDescription()); 
    }
    
    public function testAttributeMapping()
    {
        $this->assertClassHasAttribute('mapping', 'Application\Entity\Events');
    }




}
