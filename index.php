<?php
/**
* This index file is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

// Path to your settings file
require 'etc/nwf/settings.php';
require 'core/Autoloader.class.php';
require 'vendor/autoload.php';

spl_autoload_register(null, false);
spl_autoload_extensions('.class.php .controller.php .model.php .api.php .admin.controller.php .helper.php');
spl_autoload_register(__NAMESPACE__ . "\Autoloader::core");

// Create the web application
$app = Application::getInstance();