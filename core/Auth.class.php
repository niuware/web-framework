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
 * Handles the authentication session variables
 */
final class Auth {
    
    /**
     * Starts the authentication static session variables
     */
    public static function start() {
        
        session_start();
        
        self::requireAuth(false);
        self::requireAuth(false, 'admin');
    }
    
    /**
     * Sets if the current request requires user authentication
     * @param bool $value   
     * @param string $mode  Type of session (either 'main', or 'admin')
     */
    public static function requireAuth(bool $value, string $mode = 'main') {
        
        $_SESSION['nwf_auth_' . SESSION_ID . '_' . $mode . '_' . session_id()] = $value;
    }
    
    /**
     * Returns if the current request requires user authentication 
     * @param string $mode Type of session (either 'main', or 'admin')
     * @return bool
     */
    public static function useAuth(string $mode = 'main') : bool {
        
        return $_SESSION['nwf_auth_' . SESSION_ID . '_' . $mode . '_' . session_id()] ?? false;
    }
    
    /**
     * Returns if the current request has a verified user authentication
     * @param string $mode Type of session (either 'main', or 'admin')
     * @return bool
     */
    public static function verifiedAuth(string $mode = 'main') : bool {
        
        return $_SESSION['nwf_auth_' . SESSION_ID . '_' . $mode . '_' . '_login_' . session_id()] ?? false;
    }
    
    /**
     * Sets the user authentication to true
     * @param string $mode Type of session (either 'main', or 'admin')
     */
    public static function grantAuth(string $mode = 'main') {
        
        $_SESSION['nwf_auth_' . SESSION_ID . '_' . $mode . '_' . '_login_' . session_id()] = true;
    }
    
    /**
     * Sets the user authentication to false
     * @param string $mode Type of session (either 'main', or 'admin')
     */
    public static function revokeAuth(string $mode = 'main') {
        
        $_SESSION['nwf_auth_' . SESSION_ID . '_' . $mode . '_' . '_login_' . session_id()] = false;
    }
    
    /**
     * Adds a value to the current session
     * @param string $name String name of the value
     * @param type $value
     * @param type $mode Type of session (either 'main', or 'admin')
     */
    public static function add(string $name, $value, string $mode = 'main') {
        
        $_SESSION['nwf_user_' . SESSION_ID . '_' . $mode . '_' . $name . '_' . session_id()] = $value;
    }
    
    /**
     * Verifies if the session has a value
     * @param string $name Name of the value to search
     * @param type $mode Type of session (either 'main', or 'admin')
     * @return type
     */
    public static function has(string $name, string $mode = 'main') {
        
        return isset($_SESSION['nwf_user_' . SESSION_ID . '_' . $mode . '_' . $name . '_' . session_id()]);
    }
    
    /**
     * Removes a value from the session
     * @param string $name
     * @param type $mode Type of session (either 'main', or 'admin')
     */
    public static function remove(string $name, string $mode = 'main') {
        
        unset($_SESSION['nwf_user_' . SESSION_ID . '_' . $mode . '_' . $name . '_' . session_id()]);
    }
    
    /**
     * Returns a value from the session
     * @param string $name
     * @param type $mode Type of session (either 'main', or 'admin')
     * @return type
     */
    public static function get(string $name, string $mode = 'main') {
        
        if (self::has($name, $mode)) {
            
            return $_SESSION['nwf_user_' . SESSION_ID . '_' . $mode . '_' . $name . '_' . session_id()];
        }
        else {
            
            return null;
        }
    }
    
    /**
     * Unset session variables by name
     * @param type $filter Prefix of the session variables name
     * @param type $mode Type of session (either 'main', or 'admin')
     */
    private static function destroyWithFilter($filter, $mode) {
        
        $prefix = $filter . '_' . SESSION_ID . '_' . $mode . '_';
        $prefixLength = strlen($prefix);
        
        foreach ($_SESSION as $var => $value) {
            
            if (substr($var, 0, $prefixLength) === $prefix) {
                
                unset($_SESSION[$var]);
            }
        }
    }
    
    /**
     * Destroys all user custom session variables
     * @param type $mode Type of session (either 'main', or 'admin')
     */
    public static function destroy(string $mode = 'main') {
        
        self::destroyWithFilter('nwf_user', $mode);
    }
    
    /**
     * Destroys all user custom and authentication session variables
     * @param type $mode Type of session (either 'main', or 'admin')
     */
    public static function end(string $mode = 'main') {
        
        self::destroyWithFilter('nwf_user', $mode);
        self::destroyWithFilter('nwf_auth', $mode);
    }
}
