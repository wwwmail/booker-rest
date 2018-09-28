<?php

namespace tests;

use Application\Entity\Rooms;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class DataTest extends TestCase {

 public function testFailurer()
    {
        $this->assertClassHasAttribute('mapping', 'Application\Entity\Rooms');
    }
    
     public function testFailure2()
    {
        $this->assertContainsOnly('string', array('1', '2', 3));
    }
    
     public function testFailure3()
    {
        $this->assertContainsOnlyInstancesOf('Foo', array(new Foo(), new Bar(), new Foo()));
    }
    public function testFailure4()
    {
        $this->assertCount(0, array('foo'));
    }
    
     public function testFailure5()
    {
        $this->assertEmpty(array('foo'));
    }
    
    public function testFailure6()
    {
        $this->assertEquals(array('a', 'b', 'c'), array('a', 'c', 'd'));
    }
    
    
     public function testFailure7()
    {
        $this->assertFalse(TRUE);
    }
    
      public function testFailure8()
    {
        $this->assertFileExists('/path/to/file');
    }
    
     public function testFailure9()
    {
        $this->assertGreaterThan(2, 1);
    }
    
     public function testFailure10()
    {
        $this->assertGreaterThanOrEqual(2, 1);
    }
    
    
     public function testFailure11()
    {
        $this->assertInstanceOf('RuntimeException', new Exception);
    }
    
     public function testFailure()
    {
        $this->assertNull('foo');
    }
    
      public function testRangeFailure()
    {
        $this->assertSelectCount('foo bar', array('>'=>6, '<'=>8), $this->xml);
    }
    
        public function testFailure44()
    {
        $this->assertTrue(FALSE);
    }

}
