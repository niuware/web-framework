<?php

namespace Niuware\WebFramework {
    
    final class ModelQuery  {
        
        private $defaultTable;
        private $sql;
        
        function __construct(Database $dbObj = null) {
            
            if ($dbObj == null) {
                
                $this->sql = new Database();
            } else {
                
                $this->sql = $dbObj;
            }
            
            $this->defaultTable = "";
        }
        
        public function defaultTable($tableName) {
            
            $this->defaultTable = filter_var($tableName, FILTER_SANITIZE_STRING);
        }
        
        public function getDefaultTable() {
            
            return $this->defaultTable;
        }
        
        public function sql() {
            
            return $this->sql;
        }
        
        private function getRandSalt() {
            
            list($useg, $seg) = explode(' ', microtime());

            $seed = (float)$seg + ((float)$useg * 100634);

            mt_srand($seed);

            return mt_rand();
        }
        
        private function insertQuery($params, $values = array()) {
            
            $query = substr($params, 0, -1);
            
            if ($query!== false) {
                
                $this->sql->query("INSERT INTO " . $this->defaultTable . " VALUES (" . $query . ")", $values);
            }
        }
        
        private function updateQuery($params, $permasalt, $mArray = array()) {
            
            $query = substr($params, 0, -1);
            
            if ($query !== false) {
                
                $this->sql->query("UPDATE " . $this->defaultTable . " SET " . $query . " WHERE permasalt = '" . $permasalt . "'", $mArray);
            }
        }
        
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
        
        private function orderByFilter($orderBy, $order) {
            
            $orderFilter = "";
            
            if ($orderBy!= "") {
                
                $orderFilter = " ORDER BY " . $orderBy . " ";
                $orderFilter.= $order;
            }
            
            return $orderFilter;
        }
        
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
        
        public function selectPermastring($baseString) {
            
            $filter = $this->filterSpecial($baseString);
            
            $alphanum = preg_replace("/[^A-Za-z0-9 ]/", '', $filter); 
            
            return str_replace(" ", "-", $alphanum);
        }
        
        public function selectModel($orderBy = "", $order = "DESC", $limit = array()) {
            
            $limitFilter = $this->limitFilter($limit);
            
            $orderFilter = $this->orderByFilter($orderBy, $order);
            
            $this->sql->query("SELECT * FROM " . $this->defaultTable . $orderFilter . $limitFilter);
        }
        
        public function selectModelById($id, $idString = "nId") {
            
            $this->sql()->query("SELECT * FROM " . $this->defaultTable . " WHERE " . $idString . " = ?", array($id));
        }
        
        public function selectModelByPermasalt($permasalt) {
            
            $this->sql()->query("SELECT * FROM " . $this->defaultTable . " WHERE permasalt = ?", array($permasalt));
        }
        
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
        
        public function insertArray($array) {
            
            $params = "";
            
            for ($i = 0; $i < count($array); $i++) {
                
                $params.= "?,";
            }
            
            $this->insertQuery($params, $array);
        }
        
        public function deleteModel($permasalt) {
            
            $this->sql->query("DELETE FROM " . $this->defaultTable . " WHERE permasalt = ?", array($permasalt));
        }
        
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