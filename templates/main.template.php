<?php
namespace Niuware\WebFramework;
?>
<!DOCTYPE html>
<html>
    <head>
        <base href="<?php echo BASE_URL; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->view()->metas(); ?>
        
        <title><?php echo $this->view()->title; ?></title>
        
        <?php $this->view()->cdn(); ?>
        <?php $this->view()->js(); ?>
        
        <?php $this->view()->styles(); ?>
    </head>
    <body>
    <?php $this->view()->render(); ?>
    </body>
</html>