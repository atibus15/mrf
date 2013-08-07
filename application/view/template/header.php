<!DOCTYPE HTML>
<html lang="en">
    <head>
    	<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        
        <title><?=SYSTEM_TITLE?></title>
        <link rel="SHORTCUT ICON" href="./styles/images/icons/apply-icon.png"/>
        <link rel="stylesheet" type="text/css" href="./libraries/ext-4/resources/css/ext-all-scoped.css">
        <link rel="stylesheet" type="text/css" href="./libraries/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="./styles/application.css">
        <link rel="stylesheet" type="text/css" href="./styles/ui-style.css">
        <?php

        //render loaded css files in the controller;
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
        <div class="container" id="main-container">
            <div class="row-fluid" id="header"></div>