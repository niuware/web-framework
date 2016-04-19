<?php 

/**
* This interface is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {

    /**
    * Interface implemented by all Model classes
    */
    interface IModel {
        
        /**
        * Method for executing an INSERT transaction to the database
        * @param array $array Array describing the INSERT transaction
        */
        public function insert($array = array());
        
        /**
        * Method for executing a SELECT transaction from the database
        */
        public function select();
        
        /**
        * Method for executing a DELETE transaction from the database
        * @param string $permasalt Id of row to delete
        */
        public function delete($permasalt);
        
        /**
        * Method for executing an UPDATE transaction on the database
        * @param array $columns Associative array (column => value) for updating the row
        * @param string $permasalt Id of row to update
        */
        public function update($columns = array(), $permasalt);
    }
    
}