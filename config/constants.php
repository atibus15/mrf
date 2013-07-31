<?php 
/**
 * @Author  : atibus
 * @Date    : 07/12/2013
 * @Desc    : Constant Variables;
 **/ 

    // @DEFAULT_PAGE
    // default controller if the url page=undefined
    DEFINE('DEFAULT_PAGE','User'); 
    
    // @DEFAULT_ACTION
    // default method to execute if action not define in the url;
    // this must be inside the DEFAULT CONTROLLER

    DEFINE('DEFAULT_ACTION','homepage'); 

    DEFINE('APPS_DIR',  ROOT_DIR. "/application/");
    DEFINE('UTI_CORE',  ROOT_DIR."/core/");
    DEFINE('UTI_HELPER',ROOT_DIR."/helper/");
    DEFINE('UPLOAD_DIR',ROOT_DIR."/uploaded_files/");
    DEFINE('DB_CONFIG', ROOT_DIR."/config/database.ini");
    DEFINE('LIBRARY_DIR',ROOT_DIR."/libraries/");
    DEFINE('DATABASE',  UTI_CORE."Database.php");
    DEFINE('ERROR_LOG_FILE',ROOT_DIR."/logs/error_logs.txt");
    DEFINE('FB',        'FireBird');
    DEFINE('MYSQL',     'MySQL');
?>