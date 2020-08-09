<?php

class Model
{
    protected $tableName;
    protected $connection;
    protected $tableKeys;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        try {
            $this->connection = new PDO("mysql:host=" . $_ENV["HOSTNAME"] . ";dbname=" . $_ENV["DBNAME"], $_ENV["DBUSERNAME"], $_ENV["DBPASSWORD"]);
            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Could not connect to database");
            echo "Connection failed: " . $e->getMessage();
        }
        $this->bootstrapTableKeys();
    }

    private function bootstrapTableKeys()
    {

        $query = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = '" . $this->tableName . "';";
        $keys = [];
        foreach ($this->connection->query($query, PDO::FETCH_ASSOC) as $result) {
            array_push($keys, $result["COLUMN_NAME"]);
        }
        $this->tableKeys = $keys;
    }

    function new ($data) {
        foreach (array_keys($data) as $key) {
            $this->checkKeys($key);
        }
        foreach ($data as $key => $value) {
            $data[$key] = $this->connection->quote($value);
        }
        $query = "INSERT INTO " . $this->tableName . "(" . implode(',', array_keys($data)) . ") VALUES (\"" . implode('","', array_values($data)) . "\")";
        $this->connection->query($query);
        $id = -1;
        foreach ($this->connection->query("SELECT LAST_INSERT_ID();") as $result) {
            $id = $result;
        }
        return $id;
    }

    public function selectWhere($key, $value)
    {
        $this->checkKeys($key);
        $value = str_replace("'","\\'",$value);
        $query = "SELECT * FROM {$this->tableName} WHERE {$key} ='{$value}'";
        $result = $this->getquery($this->connection->query($query, PDO::FETCH_ASSOC));
        return $result;
    }
    public function deleteWhere($key, $value)
    {
        $this->checkKeys($key);
        $query = "DELETE FROM {$this->tableName} WHERE {$key} ='{$value}';";
        $this->getquery($this->connection->query($query));
        return true;
    }
    public function selectAll()
    {
        $query = "SELECT * FROM {$this->tableName}";
        $result = $this->getquery($this->connection->query($query, PDO::FETCH_ASSOC));
        return $result;
    }

    public function updateWhere($update, $condition)
    {

        $updateKey = array_keys($update)[0];
        $updateValue = $update[$updateKey];
        $conditionKey = array_keys($condition)[0];
        $conditionValue = $condition[$conditionKey];

        $this->checkKeys($updateKey);
        $this->checkKeys($conditionKey);
        $updateValue = str_replace("'","\\'",$updateValue);
       
        $query = "UPDATE " . $this->tableName . " SET " . $updateKey . "='" . $updateValue . "' WHERE " . $conditionKey . "=" . $conditionValue . "";
        $this->connection->query($query);

        return $this->selectWhere($conditionKey, $conditionValue);
    }

    protected function getQuery($queriedData)
    {
        $result = [];
        foreach ($queriedData as $row) {
            $object = new stdClass();
            foreach (array_keys($row) as $key) {
                $object->{$key} = $row[$key];
            }
            array_push($result, $object);
        }
        return $result;
    }
    protected function checkKeys($key)
    {
        if (!in_array($key, $this->tableKeys)) {
            throw new Exception("  Keys do not exist in table  ");
        }

    }
    protected function checkForAlphaNumericAndDash($input)
    {
        $regexp = "/^[a-zA-Z0-9-_]+$/";
        if (preg_match($regexp, $input) == 0) {
            throw new Exception("  You can only use alphanumeric characters  dashes and underscores in this input  ");
        }
    }
}
