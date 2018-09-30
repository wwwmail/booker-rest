<?php

namespace Application\Database;

use Application\Entity\Rooms;
use PDO;

class RoomsService {

    protected $connection;
    public $lastInsertId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get room by id
     * @param int $id
     * return object
     */
    public function fetchById($id)
    {
        
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT * FROM app_rooms WHERE id = :id "
                        . "AND is_active = 1"));
        $stmt->execute(['id' => (int) $id]);
        return Rooms::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Rooms());
    }
    
    /**
     * Get room by id for admin
     * @param int $id
     * return object
     */
    public function fetchByIdAdmin($id)
    {
        
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT * FROM app_rooms WHERE id = :id"));
        $stmt->execute(['id' => (int) $id]);
        return Rooms::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Rooms());
    }

    /**
     * Check if event exist in room
     * @param int $room_id
     * return array
     */
    public function checkEventsExist($room_id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_events')
                ->where('starttime > :starttime')
                ->andS('room_id = :room_id')::getSql());
        $stmt->execute(['starttime' => date('Y-m-d H:i:s'), 'room_id' => (int) $room_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all rooms use generator
     */
    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_rooms')->where('is_active = 1')::getSql());
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Rooms::arrayToEntity($row, new Rooms());
        }
    }
    
    
    /**
     * Get all rooms use generator
     */
    public function fetchAllAdmin()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT * FROM app_rooms"));
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Rooms::arrayToEntity($row, new Rooms());
        }
    }

    /**
     * Save room object
     * @param object instance of Rooms $obj
     * return bool
     */
    public function save(Rooms $obj)
    {
        if ($obj->getId() && $this->fetchByIdAdmin($obj->getId())) {
            return $this->doUpdate($obj);
        } else {
            return $this->doInsert($obj);
        }
    }

    /**
     * Update object 
     * @param object $obj
     * return bool
     */
    protected function doUpdate($obj)
    {
        $values = $obj->entityToArray();
        $update = 'UPDATE ' . $obj::TABLE_NAME;
        $where = ' WHERE id = ' . $obj->getId();
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    /**
     * Create object 
     * @param object $obj
     * return bool
     */
    protected function doInsert($obj)
    {
        $values = $obj->entityToArray();
        unset($values['id']);
        $insert = 'INSERT INTO ' . $obj::TABLE_NAME . ' ';
        if ($this->flush($insert, $values)) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * Create sql string with values and params and execute 
     * @param string $sql
     * @param string $values
     * @param string $where
     * return bool
     */
    protected function flush($sql, $values, $where = '')
    {
        $sql .= ' SET ';
        foreach ($values as $column => $value) {
            $sql .= $column . ' = :' . $column . ',';
        }

        $sql = substr($sql, 0, -1) . $where;
        $success = FALSE;
        try {
            $stmt = $this->connection->pdo->prepare($sql);
            $stmt->execute($values);
            $success = TRUE;
            $this->lastInsertId = $this->connection->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                    . $e->getMessage());
            $success = FALSE;
        } catch (Throwable $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                    . $e->getMessage());
            $success = FALSE;
        }
        return $success;
    }

    /**
     * Remove object 
     * @param object $obj
     * return bool
     */
    public function remove(Rooms $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchByIdAdmin($obj->getId())) ? FALSE : TRUE;
    }

}
