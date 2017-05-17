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
    public static function generateToken($length = 32) : string {
        
        $bytes = random_bytes($length);
        
        return bin2hex($bytes);
    }
}
