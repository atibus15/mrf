<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>e-Request.- Reauthentication</title>
        <link rel="SHORTCUT ICON" href="./styles/images/icons/coins-icon.png"/>
        <link rel="stylesheet" type="text/css" href="./libraries/ext-4/resources/css/ext-all-scoped.css">
        <?php
            if($this->loaded_css)
            {
                foreach($this->loaded_css as $stylesheet)
                {
                    echo $stylesheet; 
                }
            }
        ?>
    </head>
    <body>
        <?php 
            if($this->loaded_js)
            {
                foreach($this->loaded_js as $dynamic_js)
                {
                    echo $dynamic_js;
                }
            }
        ?>
    </body>
</html>