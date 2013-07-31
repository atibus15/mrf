<?php 

/**
 *@Author : atibus
 *@date : 06/13/2013
 *Description : Input helper.. 
 **/ 
if(!function_exists('get_post'))
{
    function get_post($input_name='_all')
    {
        if($input_name=='_all')
        {
            return $_REQUEST;
        }
        else if(isset($_REQUEST[$input_name]))
        {
            $current_input = $_REQUEST[$input_name];

            return (!is_array($current_input)) ? trim($current_input) : $current_input;
        }
        else
        {
            return false;
        }
    }
}

if(!function_exists('post'))
{
    function post($input_name='_all')
    {
        if($input_name=='_all')
        {
            return $_POST;
        }
        else if(isset($_POST[$input_name]))
        {
            $current_input = $_POST[$input_name];

            return (!is_array($current_input)) ? trim($current_input) : $current_input;
        }
        else
        {
            return false;
        }
    }
}

if(!function_exists('get'))
{
    function get($input_name)
    {
        if(isset($_GET[$input_name]))
        {
            $current_input = $_GET[$input_name];

            return (!is_array($current_input)) ? trim($current_input) : $current_input;
        }
        else
        {
            return false;
        }
    }
}

if(!function_exists('http_file'))
{
    function http_file($input_name='_all')
    {
        if($input_name=='_all')
        {
            return $_FILES;
        }
        else if(isset($_FILES[$input_name]))
        {
            $current_input = $_FILES[$input_name];

            return (!is_array($current_input)) ? trim($current_input) : $current_input;
        }
        else
        {
            return false;
        }
    }
}

?>