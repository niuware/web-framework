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
 * Hash and token creation and verification
 */
class Security {
    
    /**
     * Creates a hash from a string
     * @param string $toHash String to hash
     * @return string Hashed string
     */
    public static function hash(string $toHash) : string {
        
        return password_hash($toHash, \PASSWORD_DEFAULT);
    }
    
    /**
     * Verifies a hash against a source string
     * @param string $source String to verify
     * @param string $fromHash Hash to compare to
     * @return bool
     */
    public static function verifyHash(string $source, string $fromHash) : bool {
        
        return password_verify($source, $fromHash);
    }
    
    /**
     * Generates a cryptographic token
     * @return string Token
     */
    public static function generateToken(int $length = 32) : string {
        
        $bytes = random_bytes($length);
        
        return bin2hex($bytes);
    }
    
    /**
     * Sets the application csrf token
     * @param string $data
     * @return string
     */
    public static function getCsrfToken(string $data = null) : string {
        
        if (Auth::has('token', 'csrf') === false) {
            
            Auth::add('token', self::generateToken(), 'csrf');
        }
        
        if (Auth::has('token2', 'csrf') === false) {
            
            Auth::add('token2', self::generateToken(), 'csrf');
        }
        
        if ($data === null) {
            
            return Auth::get('token', 'csrf');
        }
        
        return hash_hmac('sha256', $data, Auth::get('token2', 'csrf'));
    }
    
    /**
     * Verifies if the provided csrf token is valid
     * @param string $token
     * @param string $data
     * @return bool
     */
    public static function verifyCsrfToken(string $token, string $data = null) : bool {
        
        if ($data === null) {
            
            return hash_equals(Auth::get('token', 'csrf'), $token);
        }
        
        $hash = hash_hmac('sha256', $data, Auth::get('token2', 'csrf'));
        
        return hash_equals($hash, $token);
    }
}
