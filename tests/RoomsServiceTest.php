<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PDO;

use Application\Database\Connection;
use Application\Database\RoomsService;
use Application\Entity\Rooms;

class RoomsServiceTest extends TestCase
{
        //use TestCaseTrait;
    use PHPUnit_Extensions_Database_TestCase_Trait;

    // инстанцировать только pdo один во время выполнения тестов для очистки/загрузки фикстуры
    static private $pdo = null;

    // инстанцировать только PHPUnit\DbUnit\Database\Connection один раз во время теста
    private $conn = null;
    


    static $room; //model новой комнаты
    private static $obj; //obj new room


    public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo === null) {
                self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }
    
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__). '/data/app_room.xml');
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
}