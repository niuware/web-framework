<?php

namespace Niuware\WebFramework;

if (!class_exists('\Twig_Extension')) {
    
    die("ERROR: Add twig to your composer.json file and run composer to use the Extension core class.");
}

final class Extension extends \Twig_Extension {
    
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

            return $functions;
        }
        
        return [];
    }
    
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
