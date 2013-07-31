<?php
    // error_reporting(0);

    DEFINE('ROOT_DIR',  dirname(__FILE__));
    include_once ROOT_DIR.'/config/constants.php';
    include_once UTI_HELPER.'session_helper.php';
    include_once UTI_HELPER.'directory_helper.php';
    include_once UTI_HELPER.'exception_helper.php';
    include_once UTI_HELPER.'input_helper.php';

    include_once UTI_CORE.'Loader.php';
    include_once UTI_CORE.'Model.php';
    include_once UTI_CORE.'FrontController.php';
    
    FrontController::createInstance()->dispatch();
?>