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
    * Executes Model based queries on the database
    */
    final class ModelQuery  {
        
        private $defaultTable;
        private $sql;
        
        /**
        * If there is no Database object injected, then
        * instance a new one
        * @param Database $dbObj? Database object
        */
        function __construct(Database $dbObj = null) {
            
            if ($dbObj == null) {
                
                $this->sql = new Database();
            } else {
                
                $this->sql = $dbObj;
            }
            
            $this->defaultTable = "";
        }
        
        /**
        * The default table name to use for executing the queries
        * @param string $tableName Name of the table
        */
        public function defaultTable($tableName) {
            
            $this->defaultTable = filter_var($tableName, FILTER_SANITIZE_STRING);
        }
        
        /**
        * Returns the name of the current used table
        * @param string Table name
        */
        public function getDefaultTable() {
            
            return $this->defaultTable;
        }
        
        /**
        * Returns  the current instance of the Database object
        * @param Database Instance of the Database object 
        */
        public function sql() {
            
            return $this->sql;
        }
        
        /**
        * Returns a random sequence number string. Used for creating
        * unique row id values that can be shared to public without sharing 
        * the actual primary key value
        * @param string Random sequence
        */
        private function getRandSalt() {
            
            list($useg, $seg) = explode(' ', microtime());

            $seed = (float)$seg + ((float)$useg * 100634);

            mt_srand($seed);

            return mt_rand();
        }
        
        /**
        * Executes an INSERT query
        * @param string $params String of column names
        * @param array $values Array of column values
        */
        private function insertQuery($params, $values = array()) {
            
            $query = substr($params, 0, -1);
            
            if ($query!== false) {
                
                $this->sql->query("INSERT INTO " . $this->defaultTable . " VALUES (" . $query . ")", $values);
            }
        }
        
        /**
        * Executes an UPDATE query
        * @param string $params String of column names
        * @param string $permasalt String of row Id 
        * @param array $values Array of column values
        */
        private function updateQuery($params, $permasalt, $mArray = array()) {
            
            $query = substr($params, 0, -1);
            
            if ($query !== false) {
                
                $this->sql->query("UPDATE " . $this->defaultTable . " SET " . $query . " WHERE permasalt = '" . $permasalt . "'", $mArray);
            }
        }
        
        /**
        * Sets the LIMIT restriction for a SELECT
        * @param array $limit Limit restriction (start, end)
        */
        public function limitFilter($limit = array("n")) {
            
            $limitFilter = "";
            
            if (is_numeric($limit[0]))
            {
                $limitFilter = " LIMIT " . $limit[0];
            
                if (isset($limit[1]) && is_numeric($limit[1])) {

                    $limitFilter.= ", " . $limit[1];
                }
            }
            
            return $limitFilter;
        }
        
        /**
        * Sets the ORDER BY restriction for a SELECT query
        * @param string $orderBy Column for ordering
        * @param string $order DESC or ASC
        */
        private function orderByFilter($orderBy, $order) {
            
            $orderFilter = "";
            
            if ($orderBy!= "") {
                
                $orderFilter = " ORDER BY " . $orderBy . " ";
                $orderFilter.= $order;
            }
            
            return $orderFilter;
        }
        
        /**
        * Replaces common ASCII extended characters to similar ASCII non-extended ones
        * @param string $strIn String to filter
        * @return string Replaced string
        */
        private function filterSpecial($strIn) {

            $spChars = array(
                        "á","Á","â","Â","à","À","ä","Ä","é","É","ê","Ê","è","È",
                        "ë","Ë","í","Í","ì","Ì","ï","Ï","î","Î","ó","Ó","ò","Ò",
                        "ô","Ô","ö","Ö","ú","Ú","ü","Ü","û","Û","ù","Ù","ç","Ç",
                        "ñ","Ñ");

            $spCharsRemove = array(
                        "a","A","a","A","a","A","a","A","e","E","e","E","e","E",
                        "e","E","i","I","i","I","i","I","i","I","o","O","o","O",
                        "o","O","o","O","u","U","u","U","u","U","u","U","c","C",
                        "n","N");

            $strFormat	= str_replace($spChars, $spCharsRemove, $strIn);

            return $strFormat;
        }
        
        /**
        * Returns a unique random sequence for the current table, to use for the 
        * insertion of a new row
        * @return string Random sequence string
        */
        public function selectPermasalt() {
            
            $saltExists = true;

            while ($saltExists)
            {
                $randSalt = $this->getRandSalt();

                $this->sql->query("SELECT permasalt FROM " . $this->defaultTable . " WHERE permasalt = ?", array($randSalt));

                if ($this->sql->count() == 0)
                {
                    $saltExists = false;
                }
            }

            return $randSalt;
        }
        
        /**
        * Removes all non alphanumeric characters from a string
        * @param string $baseString Input string
        * @return string Filtered string
        */
        public function selectPermastring($baseString) {
            
            $filter = $this->filterSpecial($baseString);
            
            $alphanum = preg_replace("/[^A-Za-z0-9 ]/", '', $filter); 
            
            return str_replace(" ", "-", $alphanum);
        }
        
        /**
        * Executes a SELECT query based on a Model
        * @param string $orderBy Column for ordering
        * @param string $order DESC or ASC
        * @param array $limit Limit restriction (start, end)
        */
        public function selectModel($orderBy = "", $order = "DESC", $limit = array()) {
            
            $limitFilter = $this->limitFilter($limit);
            
            $orderFilter = $this->orderByFilter($orderBy, $order);
            
            $this->sql->query("SELECT * FROM " . $this->defaultTable . $orderFilter . $limitFilter);
        }
        
        /**
        * Executes a SELECT query by Id, based on a Model
        * @param int $id Row id
        * @param string $idString Name of the Id column
        */
        public function selectModelById($id, $idString = "nId") {
            
            $this->sql()->query("SELECT * FROM " . $this->defaultTable . " WHERE " . $idString . " = ?", array($id));
        }
        
        /**
        * Executes a SELECT query by permasalt, based on a Model
        * @param string $permasalt Row permasalt
        */
        public function selectModelByPermasalt($permasalt) {
            
            $this->sql()->query("SELECT * FROM " . $this->defaultTable . " WHERE permasalt = ?", array($permasalt));
        }
        
        /**
        * Executes an INSERT query based on a Model
        * @param Model $model Model to insert
        */
        public function insertModel($model) {
            
            $params = "";
            $modelArray = get_object_vars($model);

            foreach ($modelArray as $key => $value) {

                if (strpos($key, ':') === false) {
                    
                    $params.= ":" . $key . ",";
                    
                    $this->sql->getStatement()->bindParam(":" . $key, $value);
                }
            }
            
            $this->insertQuery($params, $modelArray);
        }
        
        /**
        * Executes an INSERT query from array values
        * @param array $array Values to insert
        */
        public function insertArray($array) {
            
            $params = "";
            
            for ($i = 0; $i < count($array); $i++) {
                
                $params.= "?,";
            }
            
            $this->insertQuery($params, $array);
        }
        
        /**
        * Executes a DELETE query
        * @param string $permasalt Row Id
        */
        public function deleteModel($permasalt) {
            
            $this->sql->query("DELETE FROM " . $this->defaultTable . " WHERE permasalt = ?", array($permasalt));
        }
        
        /**
        * Executes an UPDATE query based on a Model
        * @param Model $model Model to update
        * @param string $permasalt Row Id
        * @param array $columns Name of the columns to update
        */
        public function updateModel($model, $permasalt, $columns = array()) {
            
            $params = "";
            $modelArray = get_object_vars($model);
            
            if (is_array($columns)) {
            
                $mArray = array_intersect_key($columns, $modelArray);

                foreach ($mArray as $key => $value) {

                    if (strpos($key, ':') === false) {

                        $params.= $key . " = :" . $key . ",";

                        $this->sql->getStatement()->bindParam(":" . $key, $value);
                    }
                }
                
                $this->updateQuery($params, $permasalt, $mArray);
            }
        }
    }
}