<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

use Application\Database\Connection;
use Application\Database\RoomsService;

abstract class MyApp_Tests_DatabaseTestCase extends TestCase
{
    use TestCaseTrait;

    // инстанцировать только pdo один во время выполнения тестов для очистки/загрузки фикстуры
    static private $pdo = null;

    // инстанцировать только PHPUnit\DbUnit\Database\Connection один раз во время теста
    private $conn = null;
    
//    private $db = array(
//            'host' => 'localhost',
//            'dbname' => $GLOBALS['DB_DBNAME'],
//            'user' => $GLOBALS['DB_USER'],
//            'pwd' => $GLOBALS['DB_PASSWD'],
//            'errmode' => 1,
//            'driver' => 'mysql',
//        );

    static $room; //объект нового пользователя


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

    }
    
    public function testReadAllGetRooms()
    {
        //проверяем, что массив данных из БД не пуст
        $this->assertNotEmpty(static::$user->fetchAll());
    }
    
}