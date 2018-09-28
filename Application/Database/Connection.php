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

    public function makeDsn($config)
    {
      return  $dsn = "{$config['driver']}:dbname={$config['dbname']};host={$config['host']}";
    }

}
