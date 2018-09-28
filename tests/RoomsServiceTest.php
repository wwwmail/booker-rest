<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PDO;

use Application\Database\Connection;
use Application\Database\RoomsService;
use Application\Entity\Rooms;
use tests\TrunsactionFiendlyDatabaseTestCase;
//class RoomsServiceTest extends \PHPUnit_Extensions_Database_TestCase
class RoomServiceTest extends TrunsactionFiendlyDatabaseTestCase
{

   function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);


        $this->pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
        $this->pdo->exec('SET foreign_key_checks = 0'); 
        $this->con = $this->createDefaultDBConnection($this->pdo, 'php');

        $this->pdo->beginTransaction();
    }


private $pdo = null;

private $con = null;


public function getConnection()
{
    return $this->con;
}



static $room; //model 
private static $obj; //obj i

public function getConnection3()
{
    $pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );

    return $this->createDefaultDBConnection($pdo, 'php');
}    
public function getConnection2()
{
    if ($this->conn === null) {
        if (self::$pdo === null) {
            self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            self::$pdo->beginTransaction();
        }
        self::$pdo->exec('SET foreign_key_checks = 0');  

        $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);

    }

    self::$pdo->beginTransaction();

    return $this->conn;
}



protected function getDataSet()
{
    return $this->createFlatXMLDataSet(dirname(__FILE__). '/data/app_rooms.xml');
}


public function tearDown()
{
    $this->pdo->rollBack();
    $this->pdo->beginTransaction();
}

public static function setUpBeforeClass()
{
    $dbConf = array(
        'host' => 'localhost',
        'dbname' => $GLOBALS['DB_DBNAME'],
        'user' => $GLOBALS['DB_USER'],
        'pwd' => $GLOBALS['DB_PASSWD'],
        'errmode' => 1,
        'driver' => 'mysql',
    );
    $db = new Connection($dbConf);


    $room['name'] = 'room 1';

    //      self::$trait = new TestCaseTrait();
    self::$room = new RoomsService($db);
    self::$obj = new Rooms();
}

public function testReadAllGetRooms()
{
    $this->assertNotEmpty(static::$room->fetchAll());
}

public function testSaveNewTrue()
{
    $testArray = ['name'=>'test room name', 'id'=>''];
    $testArray = $this->getDataSet();
    $test =  Rooms::arrayToEntity($testArray,  new Rooms());

    $this->assertTrue(static::$room->save($test));
}

public function saveNew()
{
    $query  = $this->connectDb()->prepare("INSERT INTO app_room (`name`) VALUES (?) ");
    return $query->execute([$this->name]);
}

public function __destruct()
{
  $this->pdo->rollBack();
}
}
