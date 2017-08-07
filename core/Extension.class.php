<?php

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

if (!class_exists('\Twig_Extension')) {
    
    die("ERROR: Add twig to your composer.json file and run composer to use the Extension core class.");
}

/**
 * Add custom functions/filters for use in twig templates
 */
final class Extension extends \Twig_Extension {
    
    /**
     * Load Twig functions
     * @return array
     */
    public function getFunctions() {
        
        if (class_exists('Niuware\WebFramework\Helpers\TwigFunctions')) {
        
            $reflectionClass = new \ReflectionClass('Niuware\WebFramework\Helpers\TwigFunctions');

            $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

            $functions = [];
            
            $class = $reflectionClass->newInstance();

            foreach ($methods as $method) {

                $functions[] = new \Twig_Function($method->name, function($params = []) use ($class, $method) { 

                                    return $class->{$method->name}($params);

                                });
            }
            
            $functions[] = new \Twig_Function('csrfToken', function($params = null) {

                echo '<input type="hidden" name="csrf_token" value="' . Security::getCsrfToken($params) . '" />';
            });
            
            $functions[] = new \Twig_Function('url', function($url = null, $mode = 'main') {
                
                $modeReal = 'main';
                $path = BASE_URL;
                $home = HOMEPAGE;
                
                if ($mode === 'admin') {
                    
                    $modeReal = 'admin';
                    $path = BASE_URL_ADMIN;
                    $home = HOMEPAGE_ADMIN;
                }
                
                $fullPath = $path . $home;
                
                $urlAction = explode('/', $url);

                if (isset(Routes::$views[$modeReal][$urlAction[0]])) {

                    $fullPath = $path . $url;
                }

                echo $fullPath;
            });

            return $functions;
        }
        
        return [];
    }
    
    /**
     * Load Twig filters
     * @return array
     */
    public function getFilters() {
        
        if (class_exists('Niuware\WebFramework\Helpers\TwigFilters')) {
        
            $reflectionClass = new \ReflectionClass('Niuware\WebFramework\Helpers\TwigFilters');

            $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

            $functions = [];
            
            $class = $reflectionClass->newInstance();

            foreach ($methods as $method) {

                $functions[] = new \Twig_Filter($method->name, function($params = []) use ($class, $method) { 

                                    return $class->{$method->name}($params);

                                });
            }

            return $functions;
        }
        
        return [];
    }
}
