<?php

namespace Niuware\WebFramework\Models {

    interface IModel {
        
        public function insert($array = array());
        public function select();
        public function delete($permasalt);
        public function update($columns, $permasalt);
    }
    
}