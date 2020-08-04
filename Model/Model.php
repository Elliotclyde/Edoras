<?php

class Model
{
    protected $tableName;
    protected $connection;

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

    }

    public function new($data){
      foreach(array_keys($data) as $key){
        $this->checkForAlphaNumericAndDash($key);
      }
      $query = "INSERT INTO ".$this->tableName."(".implode(',',array_keys($data)).") VALUES (\"".implode('","',array_values($data))."\")";
      $this->connection->query($query);
      return true;
    }

    public function selectWhere($key, $value)
    {
      $this->checkForAlphaNumericAndDash($key);
      $query = "SELECT * FROM {$this->tableName} WHERE {$key} ='{$value}'";
        $result = $this->getquery($this->connection->query($query,PDO::FETCH_ASSOC));
        return $result;
    }

    public function updateWhere($update, $condition)
    {
        $updateKey = array_keys($update)[0];
        $updateValue = $update[$updateKey];
        $conditionKey = array_keys($condition)[0];
        $conditionValue = $condition[$conditionKey];
        
        $this->checkForAlphaNumericAndDash($updateKey);
        $this->checkForAlphaNumericAndDash($conditionKey);

        $query = "UPDATE ".$this->tableName." SET ".$updateKey."='".$updateValue."' WHERE ".$conditionKey."=".$conditionValue."";
        $this->connection->query($query);

        return $this->selectWhere($conditionKey, $conditionValue);
    }

    public function getKeys(){
        $query = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='{$this->tableName}';";
        $result = [];
        foreach ($this->connection->query($query,PDO::FETCH_ASSOC) as $row){
            array_push($result,$row['COLUMN_NAME']);
        }
        return $result;
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
    protected function checkForAlphaNumericAndDash($input)
    {
        $regexp = "/^[a-zA-Z0-9-_]+$/";
        if (preg_match($regexp, $input)==0) {throw new Exception("  You can only use alphanumeric characters  dashes and underscores in this input  ");}
    }
}