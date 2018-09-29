<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Application\Database\Connection;
use Application\Database\EventsService;
use Application\Entity\Events;
use PDO;

class EventsServiceTest extends TestCase {

    private static $event; //model 
    private static $obj; //obj i
    private static $db;
    private $testArray = ['id' => '',
        'recursion' => '0',
        'recursion_id' => '0',
        'user_id' => '2',
        'room_id' => '2',
        'description' => 'some description',
        'date' => '2045-08-08',
        'starttime' => '2045-08-08 12:29:37',
        'endtime' => '2045-08-08 13:29:37',
        'created' => '0000-00-00 00:00:00',
    ];
    private $testArray2 = ['id' => '',
        'recursion' => '0',
        'recursion_id' => '0',
        'user_id' => '2',
        'room_id' => '2',
        'description' => 'some description',
        'date' => '2046-08-08',
        'starttime' => '2046-08-08 12:29:37',
        'endtime' => '2046-08-08 13:29:37',
        'created' => '0000-00-00 00:00:00',
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

        self::$event = new EventsService(self::$db);
        self::$obj = new Events();

        //var_dump($this->pdo->beginTransaction()); 
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $this->pdo->beginTransaction();
    }

    public function testReadAllGetEvents()
    {
        $this->assertNotEmpty(static::$event->fetchAll());
    }

    public function testSaveNewTrue()
    {

        $test = Events::arrayToEntity($this->testArray, new Events());

        $this->assertTrue(static::$event->save($test));
    }

    public function testReadEventsById()
    {
        $id = static::$event->lastInsertId;
        $this->assertNotEmpty(static::$event->fetchById(1));
    }

    public function testTypeEventsById()
    {
        $id = static::$event->lastInsertId;
        $this->assertInternalType('object', static::$event->fetchById(1));
    }

    public function testTypeEventsAll()
    {
        $id = static::$event->lastInsertId;
        $this->assertInternalType('object', static::$event->fetchAll());
    }

    public function testFetchByRoomId()
    {
        $this->assertInternalType('array', static::$event->fetchByRoom(1));
    }

    public function testCreatSimpleEvent()
    {
        $result = static::$event->creatSimpleEvent($this->testArray2);
        $this->assertInternalType('bool', $result);
    }

    public function testTypeRemooveEvent()
    {
        $result = static::$event->removeEvent(111);
        $this->assertInternalType('bool', $result);
    }

    public function testRemoove()
    {
        $result = static::$event->removeEvent(111);
        $this->assertInternalType('bool', $result);
    }

    public function tearDown()
    {
        $this->pdo->rollBack();
        $this->pdo->beginTransaction();
    }

}
