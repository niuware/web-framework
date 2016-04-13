<?php

namespace Niuware\WebFramework
{
    require 'etc/nwf/settings.php';
    require 'core/Autoloader.class.php';
    
    spl_autoload_register(null, false);
    spl_autoload_extensions('.class.php .api.php .view.php model.php');
    spl_autoload_register(__NAMESPACE__ . "\Autoloader::core");
    
    $app = new Application();
    
    $app->start();
}