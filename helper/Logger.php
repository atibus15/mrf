<?php
    
class Logger
{
    public function __construct()
    {
        ;
    }

    public static function write($e)
    {
        $timestamp = date('m/d/Y H:i:s', time());
        $formatted_log =  $timestamp.' : --> '.$e->getMessage()."\n\rat ".$e->getFile().".. Line: ". $e->getLine()."\n\r";


        if(!file_put_contents(ERROR_LOG_FILE, $formatted_log,FILE_APPEND))
        {
            echo 'unable to write log.. make sure log file is writable.';
        }
    }
}

?>