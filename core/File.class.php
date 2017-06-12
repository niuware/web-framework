<?php

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

/**
 * File helper class
 */
final class File {
    
    private $name;
    
    private $tmp_name;
    
    private $error;
    
    private $size;
    
    private $type;
    
    public function __construct($attributes = null) {
        
        $this->set($attributes, 'name');
        $this->set($attributes, 'type');
        $this->set($attributes, 'tmp_name');
        $this->set($attributes, 'error');
        $this->set($attributes, 'size');
    }
    
    private function set($array, $name) {
        
        if (isset($array[$name])) {
            
            $this->{$name} = $array[$name];
        }
    }
    
    public function __get($name) {
        
        if (isset($this->$name)) {
            
            return $this->$name;
        }
    }
    
    /**
     * Gets the path for a file based on the MIME type
     * @param type $path
     * @return type
     */
    private function getFilePath($path) {
        
        $uploadPath = '';
        $subpath = 'other/';
        
        if ($path === 'auto') {
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            
            $type = finfo_file($finfo, $this->tmp_name);
            
            if (strpos($type, 'image') !== false) {
                
                $subpath = 'image/';
            }
            elseif (strpos($type, 'video') !== false) {
                
                $subpath = 'video/';
            }
            elseif (strpos($type, 'audio') !== false) {
                
                $subpath = 'audio/';
            }
            
            $uploadPath = 'public/assets/' . $subpath;
        }
        else {
            
            $uploadPath = $path;
        }
        
        return $uploadPath;
    }
    
    /**
     * Gets the filename and extension of a string filename
     * @param type $fileName
     * @param type $name
     * @param type $extension
     */
    private function getFileName($fileName, &$name, &$extension) {
        
        $names = explode('.', $fileName);
        $realName = '';
        
        $lastDot = count($names) - 1;
        
        for ($i = 0; $i < $lastDot; $i++) {
            
            $realName.= $names[$i];
        }
        
        $name = $realName;
        $extension = $names[$lastDot];
    }
    
    /**
     * Moves a file to the destination path
     * @param string $name
     * @param string $path
     */
    public function move($name = 'same', $path = 'auto') {
        
        $fileName = $this->name;
        
        $realFileName = '';
        $realFileExtension = '';

        $this->getFileName($fileName, $realFileName, $realFileExtension);
        
        if ($name !== 'same' && !empty($name)) {
            
            $fileName = $name . '.' . $realFileExtension;
            $realFileName = $name;
        }
        
        $uploadPath = $this->getFilePath($path);
        
        if (!file_exists($uploadPath)) {

            if (!mkdir($uploadPath, 0777, true)) {
                
                return false;
            }
        }
        
        $filePath = $uploadPath . $fileName;
        
        if (file_exists($filePath)) {
            
            $fileName = $realFileName . '_' . date('YmdHmss') . '.' . $realFileExtension;
            
            $filePath = $uploadPath . $fileName;
        }
        
        return move_uploaded_file($this->tmp_name, $filePath);
    }
}