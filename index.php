<?php
/**
* This index file is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework
{
    require 'etc/nwf/settings.php';
    require 'core/Autoloader.class.php';
    
    spl_autoload_register(null, false);
    spl_autoload_extensions('.class.php .interface.php .api.php .view.php model.php');
    spl_autoload_register(__NAMESPACE__ . "\Autoloader::core");
    
    // Create the web application
    $app = new Application();
}