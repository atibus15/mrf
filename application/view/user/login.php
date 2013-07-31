<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>e-Request.- login</title>
        <link rel="SHORTCUT ICON" href="./styles/images/icons/coins-icon.png"/>
        <link rel="stylesheet" type="text/css" href="./libraries/ext-4/resources/css/ext-all-scoped.css">
        <link rel="stylesheet" type="text/css" href="./libraries/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="./libraries/bootstrap/css/bootstrap-responsive.css">

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
        <div class="container">
 
          <form class="form-signin" autocomplete="off">
            <h2 class="form-signin-heading">e-Request Login</h2>
            <div class="control-group">
                <label class="control-label" for="username">Username:</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input type="text" class="input-xlarge" id="username" name="username">
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password">Password:</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on"><i class="icon-qrcode"></i></span>
                        <input type="password" class="input-xlarge" id="password">
                    </div>
                </div>
            </div>
            <button class="btn btn-large btn-primary" onclick="return false;" type="submit" id="login-btn">Login</button>
          </form>
        </div>

        <script src="./libraries/jquery/jquery-1.10.2.min.js"></script>
        

        <!-- reset extjs scope css fixed conflicting in bootstrap -->
        <script type="text/javascript">Ext = {buildSettings:{"scopeResetCSS":true}};</script>


        <?php 
            if($this->loaded_js)
            {
                foreach($this->loaded_js as $dynamic_js)
                {
                    echo $dynamic_js;
                }
            }
        ?>
        <script type="text/javascript" src="./scripts/js/common.js"></script>
    </body>
</html>