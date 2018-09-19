<?php

namespace Application\Database;

use Exception;
use PDO;

class Connection {

    const ERROR_UNABLE = 'ERROR: no database connection';

    public $pdo;

    public function __construct(array $config)
    {
        if (!isset($config['driver'])) {
            $message = __METHOD__ . ' : '
                    . self::ERROR_UNABLE . PHP_EOL;
            throw new Exception($message);
        }
        $dsn = $this->makeDsn($config);

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['pwd'], [PDO::ATTR_ERRMODE => $config['errmode']]);
            return TRUE;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return FALSE;
        }
    }

    public static function factory(
    $driver, $dbname, $host, $user, $pwd, array $options = array())
    {
        $dsn = $this->makeDsn($config);
        try {
            return new PDO($dsn, $user, $pwd, $options);
        } catch (PDOException $e) {
            error_log($e->getMessage);
        }
    }

    public function makeDsn($config)
    {
        $dsn = $config['driver'] . ':';
        unset($config['driver']);
        foreach ($config as $key => $value) {
            $dsn .= $key . '=' . $value . ';';
        }

        return substr($dsn, 0, -1);
    }

}
