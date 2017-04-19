<?php
namespace Niuware\WebFramework;
?>
<!DOCTYPE html>
<html>
    <head>
        <base href="<?php echo BASE_URL; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->controller->metas(); ?>
        
        <title><?php echo $this->controller->title; ?> | Admin Side Site</title>
        
        <?php $this->controller->cdn(); ?>
        <?php $this->controller->js(); ?>
        
        <?php $this->controller->styles(); ?>
    </head>
    <body>