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
    
    private $original_request = [];
    
    public $filetype;
    
    public function __construct($attributes = null) {
        
        $this->set($attributes, 'name');
        $this->set($attributes, 'type');
        $this->set($attributes, 'tmp_name');
        $this->set($attributes, 'error');
        $this->set($attributes, 'size');
    }
    
    private function set($array, $name, $direct = false) {
        
        if ($direct === true) {
        
            $this->$name = $array[$name];
            
            return;
        }
        
        if (isset($array[$name])) {
            
            $this->original_request[$name] = $array[$name];
        }
    }
    
    public function __get($name) {
        
        if (isset($this->$name)) {
            
            return $this->$name;
        }
        
        return null;
    }
    
    /**
     * Gets the path for a file based on the MIME type
     * @param type $path
     * @return type
     */
    private function getFilePath($path, $mimeTypeSuffix) {
        
        $mimeTypePath = '';
        
        if ($mimeTypeSuffix === true) {
            
            $subpath = 'other/';
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            
            $this->filetype = finfo_file($finfo, $this->original_request['tmp_name']);
            
            if (strpos($this->filetype, 'image') !== false) {
                
                $subpath = 'image/';
            }
            elseif (strpos($this->filetype, 'video') !== false) {
                
                $subpath = 'video/';
            }
            elseif (strpos($this->filetype, 'audio') !== false) {
                
                $subpath = 'audio/';
            }
        }
        
        if ($path === 'auto') {
            
            $uploadPath = 'public/assets/';
        }
        else {
            
            if (substr($path, -1) !== '/') {
                
                $path.= '/';
            }
            
            $uploadPath = $path;
        }
            
        $uploadPath.= $mimeTypePath;
        
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
     * Updates the filename if necessary
     * @param type $fileName
     * @param type $realFileExtension
     * @param type $realFileName
     */
    private function updateFileName(&$fileName, &$finalFileName, &$realFileExtension, &$realFileName) {
        
        if ($fileName !== '') {
            
            if ($fileName === 'unique') {
                
                $uniqueName = Security::generateToken();
                
                $finalFileName = $uniqueName . '.' . $realFileExtension;
                $fileName = $uniqueName;
            }
            
            $finalFileName = $fileName . '.' . $realFileExtension;
            $realFileName = $fileName;
        }
    }
    
    /**
     * Moves a file to the destination path
     * @param string $fileName
     * @param string $path
     */
    public function save($fileName = '', $path = 'public', $mimeTypeSuffix = true) {
        
        if (empty($this->original_request['tmp_name'])) {
            
            return null;
        }
        
        $finalFileName = $this->original_request['name'];
        
        $realFileName = '';
        $realFileExtension = '';

        $this->getFileName($finalFileName, $realFileName, $realFileExtension);
        
        $this->updateFileName($fileName, $finalFileName, $realFileExtension, $realFileName);
        
        $uploadPath = $this->getFilePath($path, $mimeTypeSuffix);
        
        if (!file_exists($uploadPath)) {

            if (!mkdir($uploadPath, 0777, true)) {
                
                return false;
            }
        }
        
        $filePath = $uploadPath . $finalFileName;
        
        if (file_exists($filePath)) {
            
            $finalFileName = $realFileName . '_' . date('YmdHmss') . '.' . $realFileExtension;
            
            $filePath = $uploadPath . $finalFileName;
        }
        
        if(move_uploaded_file($this->original_request['tmp_name'], $filePath)) {
            
            $this->set(['filename' => $finalFileName], 'filename', true);
            $this->set(['filepath' => $uploadPath], 'filepath', true);
            $this->set(['filenameAndPath' => $filePath], 'filenameAndPath', true);
            
            $publicUrl = "";
            
            if (strpos($filePath, 'public/') !== false) {
                
                $publicUrl = BASE_URL . $filePath;
            }
            
            $this->set(['public_url' => $publicUrl], 'public_url', true);
            
            return $this;
        }
        
        return null;
    }
    
    /**
     * Deletes a file from disk
     * @param string $file Path of the file
     * @return boolean Delete success
     */
    public function delete($file) {
        
        $path = str_replace(\Niuware\WebFramework\BASE_URL, '', $file);
        
        $defaultPath = 'public/assets/' . $file;

        if (file_exists($path)) {

            unlink($path);
            
            return true;
        }
        else if (file_exists($file)) {
            
            unlink($file);
            
            return true;
        }
        else if (file_exists($defaultPath)) {
                
            unlink($defaultPath);
            
            return true;
        }
        
        return false;
    }
}