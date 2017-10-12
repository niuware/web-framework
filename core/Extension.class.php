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
        
        if (class_exists('\App\Helpers\TwigFunctions')) {
        
            $reflectionClass = new \ReflectionClass('\App\Helpers\TwigFunctions');

            $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

            $functions = [];
            
            $class = $reflectionClass->newInstance();

            foreach ($methods as $method) {

                $functions[] = new \Twig_Function($method->name, function() use ($class, $method) { 

                                    return $class->{$method->name}(func_get_args());

                                });
            }
            
            $functions[] = new \Twig_Function('csrfToken', function($params = null) {

                echo '<input type="hidden" name="csrf_token" id="csrf_token" value="' . Security::getCsrfToken($params) . '" />';
            });
            
            $functions[] = new \Twig_Function('url', function($url = null, $mode = 'main') {
                
                $modeReal = 'main';
                $path = \App\Config\BASE_URL;
                $home = \App\Config\HOMEPAGE;
                
                if ($mode === 'admin') {
                    
                    $modeReal = 'admin';
                    $path = \App\Config\BASE_URL_ADMIN;
                    $home = \App\Config\HOMEPAGE_ADMIN;
                }
                
                $fullPath = $path . $home;
                
                $urlAction = explode('/', $url);

                if (isset(\App\Config\Routes::$views[$modeReal][$urlAction[0]])) {

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
        
        if (class_exists('\App\Helpers\TwigFilters')) {
        
            $reflectionClass = new \ReflectionClass('\App\Helpers\TwigFilters');

            $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

            $functions = [];
            
            $class = $reflectionClass->newInstance();

            foreach ($methods as $method) {

                $functions[] = new \Twig_Filter($method->name, function() use ($class, $method) { 

                                    return $class->{$method->name}(func_get_args());

                                });
            }

            return $functions;
        }
        
        return [];
    }
}
