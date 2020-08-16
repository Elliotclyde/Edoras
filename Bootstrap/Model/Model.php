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

    //Create new row in DB
    public function new ($data) {
        foreach (array_keys($data) as $key) {
            $this->checkKey($key);
        }
        $questionMarkList = $this->getQuestionMarksForParamList($data);
        $query  = $this->connection->prepare("INSERT INTO " . $this->tableName . "(" . implode(',', array_keys($data)) . ") VALUES (" . $questionMarkList . ");");
        $query->execute(array_values($data));
        $id = -1;
        foreach ($this->connection->query("SELECT LAST_INSERT_ID();") as $result) {
            $id = $result;
        }
        return $id;
    }

    private function getQuestionMarksForParamList($data)
    {
        $questionMarkList='';
        foreach ( array_slice($data, 0,count($data) - 1) as $key=>$value) {
            $questionMarkList .= "? , ";
        }
        $questionMarkList .= "?";
        return $questionMarkList;
    }

    //get's an array of STD classes representing columns where the key matches the value
    public function selectWhere($key, $value)
    {
        $this->checkKey($key);
        $value = str_replace("'","\\'",$value);
        $query = $this->connection->prepare("SELECT * FROM {$this->tableName} WHERE {$key} = :value");
        $query->execute([':value' => $value]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    //select by id 
    public function find($id)
    {
        $query = $this->connection->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
        $query->execute([':id' => $id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    //delete by id
    public function deleteById($id)
    {
        $query = $this->connection->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
        var_dump($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return true;
    }

    //deletes all rows where the key matches the value
    public function deleteWhere($key, $value)
    {
        $this->checkKey($key);
        $query = $this->connection->prepare("DELETE FROM {$this->tableName} WHERE {$key} = :value");
        $query->bindParam(':value', $value);
        $query->execute();
        return true;
    }

    //Select all rows
    public function selectAll()
    {
        $query = $this->connection->prepare("SELECT * FROM {$this->tableName}");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    //Update the first key value array where the second key value array exists
    public function updateWhere($updatePair, $conditionPair)
    {
        $updateKey = array_keys($updatePair)[0];
        $updateValue = $updatePair[$updateKey];
        $conditionKey = array_keys($conditionPair)[0];
        $conditionValue = $conditionPair[$conditionKey];

        $this->checkKey($updateKey);
        $this->checkKey($conditionKey);
        $updateValue = str_replace("'","\\'",$updateValue);

        $query = $this->connection->prepare("UPDATE ".$this->tableName." SET ".$updateKey." =:updateValue  WHERE ".$conditionKey."=:conditionValue");
        $query->execute([':updateValue'=> $updateValue, ':conditionValue'=> $conditionValue]);
        return true;
    }

    protected function checkKey($key)
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
