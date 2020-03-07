<?php
$connection_buffer = file_get_contents(DIRNAME . "/settings.json");
$connection_json = json_decode($connection_buffer);

class Database
{

    var $pdo;
    public $server;
    public $server_port;
    public $user;
    public $password;
    public $password_encryption;
    public $database;
    public $db_model;
    private $dbh;
    private $error;
    private $stmt;

    public function __construct()
    {

        global $connection_json;


        $this->server = $connection_json->database->server;
        $this->server_port = $connection_json->database->port;
        $this->user = $connection_json->database->user;
        $this->password_encryption = $connection_json->database->password_encryption;

        switch ($this->password_encryption) {
            case "base64":
                $this->password = base64_decode($this->password);
                break;
        }

        $this->password = $connection_json->database->password;
        $this->database = $connection_json->database->database;
        $this->db_model = $connection_json->database->driver;

        try {


            $dsn = $this->db_model . ":host=" . $this->server . ";dbname=" . $this->database;

            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }


    }

    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;

                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;

                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;

                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function resultset()
    {
        $this->execute();
        $result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->close();
        return $result;

    }

    public function resultsetObject()
    {
        $this->execute();
        $result = $this->stmt->fetch(PDO::FETCH_OBJ);
        $this->close();
        return $result;
    }

    /*
        public function resultsetClass($class)
        {
            $this->execute();
            $result = $this->stmt->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class);
            $this->close();
            return $result;
        }
    */
    public function execute()
    {
        $execute = $this->stmt->execute();
    }

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        $this->execute();
        return $this->stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * Transactions allow multiple changes to a database all in one batch.
     */
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    public function endTransaction()
    {
        return $this->dbh->commit();
    }

    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }

    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    public function close($close_connection = false)
    {
        $this->stmt->closeCursor();
        if ($close_connection) $this->dbh = null;
    }

}


?>