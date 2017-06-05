<?php
/**
* This index file is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

require_once 'core/Application.class.php';

// Create the web application
$app = Application::getInstance();

$app->run();