<?php
namespace tests;

use Application\Entity\Users;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;



class UsersTest extends TestCase
{
    private $users;
 
    protected function setUp()
    {
        $this->users = new Users();
    }

    protected function tearDown()
    {
        $this->users = NULL;
    }

    public function testSetEmail()
    {
        $this->users->setEmail('www@mail.cz');
        $this->assertEquals('www@mail.cz', $this->users->getEmail());
    }

    public function testSetFirstName()
    {
        $this->users->setFirstName('Ivan');
        $this->assertEquals('Ivan', $this->users->getFirstName());
    }


    public function testSetLastName()
    {
        $this->users->setLastName('Yasinskiy');
        $this->assertEquals('Yasinskiy', $this->users->getLastName());
    }
    
    
    public function testSetTypeLastName()
    {
        $this->users->setLastName('Yasinskiy');
        $this->assertInternalType('string', $this->users->getLastName());
    }

    
    public function testSetPassword()
    {
        $this->users->setPassword('123');
        $this->assertEquals('123', $this->users->getPassword());
    }
    
    public function testSetToken()
    {
        $this->users->setToken('qqq');
        $this->assertEquals('qqq', $this->users->getToken());
    }
    
    public function testAttributeMapping()
    {
        $this->assertClassHasAttribute('mapping', 'Application\Entity\Users');
    }
    
    
    

}
