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
}
