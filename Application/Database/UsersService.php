<?php

namespace Application\Database;

use Application\Entity\Users;
use PDO;

class UsersService {

    protected $connection;
    public $lastInsertId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get user by id
     * @param int $id
     * return object
     */
    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_users')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Users::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Users());
    }

    /**
     * Get all users use generator
     */
    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_users')::getSql());
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Users::arrayToEntity($row, new Users());
        }
    }

    /**
     * Get user by token   
     * @param string $token
     * return object
     */
    public function fetchByToken($token)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('app_users')
                        ->where('token = :token')::getSql());
        $stmt->execute(['token' => $token]);

        return Users::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Users());
    }

    /**
     * Check if exist user with this email  
     * @param string $email
     * return bool
     */
    public function checkByEmail($email)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('app_users')->where('email = :email')::getSql());
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get object user by email  
     * @param string $email
     * return object
     */
    public function fetchByEmail($email)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('app_users')->where('email = :email')::getSql());
        $stmt->execute(['email' => $email]);

        return Users::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Users());
    }

    /**
     * Save room object
     * @param object instance of Rooms $obj
     * return bool
     */
    public function save(Users $obj)
    {
        if ($obj->getId() && $this->fetchById($obj->getId())) {
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
        $email = $obj->getEmail();
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
            $this->lastInsertId = $this->connection->pdo->lastInsertId();
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

    /**
     * Remove object 
     * @param object $obj
     * return bool
     */
    public function remove(Users $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

    /**
     * Create user  
     * @param array $data
     * return array
     */
    public function createUser(array $data)
    {
        $info = array();
        if ($this->fetchByEmail($data['email'])) {

            $info = [
                'success' => false,
                'item' => false,
                'message' => 'user_exist'
            ];
            return $info;
        }

        $hash = $this->createHashPassword($data['password']);

        $newData['token'] = bin2hex(random_bytes(16));

        $updateData = array_merge($data, $newData);

        $updateUserData = Users::arrayToEntity($updateData, new Users());

        $updateUserData->setPassword($hash);

        $info = [
            'success' => true,
            'item' => $updateUserData,
        ];
        return $info;
    }

    /**
     * Create hash password 
     * @param string $password
     * return string
     */
    public function createHashPassword($password)
    {
        $random = openssl_random_pseudo_bytes(18);
        $salt = sprintf('$2y$%02d$%s', 13, // 2^n cost factor
                substr(strtr(base64_encode($random), '+', '.'), 0, 22)
        );
        $options = ['cost' => 13];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

}
