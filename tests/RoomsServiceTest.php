<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Application\Database\Connection;
use Application\Database\RoomsService;
use Application\Entity\Rooms;
use tests\TrunsactionFiendlyDatabaseTestCase;
use PDO;

class RoomsServiceTest extends TestCase {

    private $con;
    private $pdo;
    private static $db;
    private static $room;
    private static $obj;
    private $testArray = ['name' => 'test 1', 'id' => ''];

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

        self::$room = new RoomsService(self::$db);
        self::$obj = new Rooms();

        $this->pdo->beginTransaction();
    }

    public function testReadAllGetRooms()
    {
        $this->assertNotEmpty(static::$room->fetchAll());
    }

    public function testSaveNewTrue()
    {

        $test = Rooms::arrayToEntity($this->testArray, new Rooms());
        $this->assertTrue(static::$room->save($test));
    }

    public function testReadRoomsById()
    {
        $this->assertNotEmpty(static::$room->fetchById(1));
    }

    public function testTypeRoomsById()
    {
        $this->assertInternalType('object', static::$room->fetchById(1));
    }

    public function testTypeRoomsAll()
    {
        $this->assertInternalType('object', static::$room->fetchAll());
    }

    public function tearDown()
    {
        $this->pdo->rollBack();
        $this->pdo->beginTransaction();
    }

    public function __destruct()
    {
        
    }

}
