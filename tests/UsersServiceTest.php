<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Application\Database\Connection;
use Application\Database\UsersService;
use Application\Entity\Users;

class UsersServiceTest extends TestCase {

    private static $user; //model 
    private static $obj; //obj i
    private static $db;
    private $testArray = ['id' => '',
        'email' => 'testtesttest@test.cz',
        'password' => 'hash password',
        'first_name' => 'Ivan',
        'last_name' => 'Yasinskiy',
        'token' => 'token',
        'expire' => '2018-09-21 12:29:37',
        'is_admin' => '0',
        'is_active' => '0',
    ];

    public function __construct($name = null, array $data = array(), $dataName = '')
    {

        $dbConf = array(
            'host' => 'localhost',
            'dbname' => $GLOBALS['DB_DBNAME'],
            'user' => $GLOBALS['DB_USER'],
            'pwd' => $GLOBALS['DB_PASSWD'],
            'errmode' => 1,
            'driver' => 'mysql',
        );

        self::$db = new Connection($dbConf);

        $this->pdo = self::$db->pdo;
        $this->pdo->exec('SET foreign_key_checks = 0');

        self::$user = new UsersService(self::$db);
        self::$obj = new Users();

        $this->pdo->beginTransaction();
    }

    public function testReadAllGetUsers()
    {
        $this->assertNotEmpty(static::$user->fetchAll());
    }

    public function testSaveNewTrue()
    {

        $test = Users::arrayToEntity($this->testArray, new Users());

        $this->assertTrue(static::$user->save($test));
    }

    public function testReadUsersById()
    {
        $id = static::$user->lastInsertId;
        $this->assertNotEmpty(static::$user->fetchById(1));
    }

    public function testCreateUser()
    {
        $this->assertNotEmpty(static::$user->createUser($this->testArray));
    }

    public function testCheckEmailExist()
    {
        $this->assertFalse(static::$user->checkByEmail('12@mail.cz'));
    }

    public function testGetTypeByEmailExist()
    {
        $this->assertInternalType('object', static::$user->fetchByEmail('qq@mail.cz'));
    }

    public function testGetByEmailExist()
    {
        $this->assertNotEmpty('object', static::$user->fetchByEmail($this->testArray['email']));
    }

    public function testHashPassGenerate()
    {
        $this->assertInternalType('string', static::$user->createHashPassword('some pass'));
    }

    public function testTypeUsersById()
    {
        $id = static::$user->lastInsertId;
        $this->assertInternalType('object', static::$user->fetchById(1));
    }

    public function testTypeUsersAll()
    {
        $id = static::$user->lastInsertId;
        $this->assertInternalType('object', static::$user->fetchAll());
    }

    public function tearDown()
    {
        self::$db->pdo->rollBack();
        self::$db->pdo->beginTransaction();
    }

    public function __destruct()
    {
        
    }

}
