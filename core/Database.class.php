<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {
    
    /**
    * Creates a connection to a database specified
    * in the settings file
    */
    final class Database {

        private $db;
        private $dbResponse;

        function __construct() {

            // Create the PDO object and attempt a connection to the database
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

        /**
        * Executes a query to the database
        * @param string $query The query string to execute
        * @param array $params The parameters to replace in the query string
        */
        public function query($query, $params = array()) {

            $this->dbResponse = $this->db->prepare($query);

            if ($this->dbResponse != null) {

                $this->dbResponse->execute($params);
            } else {

                $this->dbResponse = new \PDOStatement();
            }
        }

        /**
        * Returns the last id from an INSERT query
        * @return string Inserted row id
        */
        public function lastInsertId($name = null) {

            return $this->db->lastInsertId($name);
        }

        /**
        * Returns the PDOStatement object
        * @return PDOStatement Instance of the PDOStatement object
        */
        public function getStatement() {

            if ($this->dbResponse == null) {
                
                $this->dbResponse = new \PDOStatement();
            }
            
            return $this->dbResponse;
        }

        /**
        * Returns a column value by index, of a row associated with the executed query
        * @param int Index of the column to return
        * @return mixed The column value
        */
        public function row($index = 0) {

            return $this->dbResponse->fetchColumn($index);
        }

        /**
        * Return the number of rows associated with the executed query
        * @return int Number of rows
        */
        public function count() {

            return $this->dbResponse->rowCount();
        }
        
        /**
        * Return the error code associated with the executed query
        * @return string Error code
        */
        public function errorCode() {
            
            return $this->db->errorCode();
        }
        
        /**
        * Returns a column value by name, in a row associated with the executed query
        * @param string $value Name of the column to get
        * @return mixed Column value
        */
        public function fetchValue($value) {
            
            $array = $this->dbResponse->fetch(\PDO::FETCH_ASSOC);
            
            return $array[$value];
        }
        
        /**
        * Returns all rows as an array, associated with the executed query
        * @return array The row values
        */
        public function fetchToSingleArray() {
            
            return array_values($this->dbResponse->fetchAll(\PDO::FETCH_COLUMN));
        }
        
        /**
        * Returns all rows as an associative array, associated with the executed query
        * @return array The (column => value) array
        */
        public function fetchToArray() {
            
            return $this->dbResponse->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        /**
        * Returns all rows as objects, associated with the executed query
        * @return array The (column->value) object array
        */
        public function fetchToObject() {
            
            return $this->dbResponse->fetchAll(\PDO::FETCH_CLASS, "\stdClass");
        }
        
        /**
        * Returns the first row as object, associated with the executed query
        * @return object The (column->value) object
        */
        public function fetchToSingleObject() {
            
            return $this->dbResponse->fetch(\PDO::FETCH_OBJ);
        }

        /**
         * Returns an object of type $model
         * @param string $model Name of the model to fetch (include full namespace)
         * @return Model Oject of the $model
         */
        public function fetchToModel($model) {

            $fetch = $this->dbResponse->fetchAll(\PDO::FETCH_CLASS, $model);

            return $fetch;
        }
        
        /**
         * Sets all model data into $obj
         * @param Model $obj Object of the model to which the data will be set
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