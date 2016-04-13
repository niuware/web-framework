<?php

namespace Niuware\WebFramework {
    
    class Database {

        private $db;
        private $dbResponse;

        function __construct() {

            try {

                $this->db = new \PDO(
                        Settings::$databases['default']['engine'] . ":dbname="
                        . Settings::$databases['default']['schema'] . "_" . constant(__NAMESPACE__ . "\DB_LANG")
                        . Settings::$databases['default']['host']
                        . Settings::$databases['default']['charset'], 
                        Settings::$databases['default']['user'], 
                        Settings::$databases['default']['pass']);
            } catch (\PDOException $e) {

                die("Error 0x101");
            }
        }

        public function query($query, $params = array()) {

            $this->dbResponse = $this->db->prepare($query);

            if ($this->dbResponse != null) {

                $this->dbResponse->execute($params);
            } else {

                $this->dbResponse = new \PDOStatement();
            }
        }

        public function lastInsertId($name = null) {

            return $this->db->lastInsertId($name);
        }

        public function getStatement() {

            if ($this->dbResponse == null) {
                
                $this->dbResponse = new \PDOStatement();
            }
            
            return $this->dbResponse;
        }

        public function row($index = 0) {

            return $this->dbResponse->fetchColumn($index);
        }

        public function count() {

            return $this->dbResponse->rowCount();
        }
        
        public function errorCode() {
            
            return $this->db->errorCode();
        }
        
        public function fetchValue($value) {
            
            $array = $this->dbResponse->fetch(\PDO::FETCH_ASSOC);
            
            return $array[$value];
        }
        
        public function fetchToSingleArray() {
            
            return array_values($this->dbResponse->fetchAll(\PDO::FETCH_COLUMN));
        }
        
        public function fetchToArray() {
            
            return $this->dbResponse->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        public function fetchToObject() {
            
            return $this->dbResponse->fetchAll(\PDO::FETCH_CLASS, "\stdClass");
        }
        
        public function fetchToSingleObject() {
            
            return $this->dbResponse->fetch(\PDO::FETCH_OBJ);
        }

        /**
         * Returns an object of type $model
         * @param string $model Name of the model to fetch (include full namespace)
         * @return BaseModel
         */
        public function fetchToModel($model) {

            $fetch = $this->dbResponse->fetchAll(\PDO::FETCH_CLASS, $model);

            return $fetch;
        }
        
        /**
         * Sets all model data into $obj
         * @param Model $obj Object of the model to set
         */
        public function fetchIntoModel(&$obj) {
            
            $fetch = $this->dbResponse->fetch(\PDO::FETCH_OBJ);
            
            if ($fetch!= false) {
                
                $properties = get_object_vars($fetch);

                foreach ($properties as $name => $value) {

                    $obj->{$name} = $value;
                }
            }
        }

    }

}