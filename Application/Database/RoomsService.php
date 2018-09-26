<?php

namespace Application\Database;

use Application\Entity\Rooms;
use PDO;
class RoomsService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_rooms')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Rooms::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Rooms());
    }
    
    public function checkEventsExist($room_id)
    {
      $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_events')
                ->where('starttime > :starttime')
                ->and('room_id = :room_id')::getSql());
       $stmt->execute(['starttime' => date('Y-m-d H:i:s'), 'room_id'=>(int) $room_id]);
       return $stmt->fetch(PDO::FETCH_ASSOC);   
    }

    

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_rooms')::getSql());
        $stmt->execute();
 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Rooms::arrayToEntity($row, new Rooms());
        }

    }

    
    

    public function save(Rooms $obj)
    {
        if ($obj->getId() && $this->fetchById($obj->getId())) {
            return $this->doUpdate($obj);
        } else {
            return $this->doInsert($obj);
        }
    }

    protected function doUpdate($obj)
    {
        $values = $obj->entityToArray();
        $update = 'UPDATE ' . $obj::TABLE_NAME;
        $where = ' WHERE id = ' . $obj->getId();
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

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

    public function remove(Rooms $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }
    

}
