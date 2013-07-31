<?php
/**
 *@Author : atibus
 *@date : 06/13/2013
 *Description : Session helper.. 
 **/ 
if(!function_exists('startSession'))
{
    function startSession()
    {
        if(!session_id())
        {
            session_start();
        }
    }
}

if(!function_exists('userSession'))
{
    function userSession($session_key)
    {
        if(isset($_SESSION[$session_key]))
        {
            return $_SESSION[$session_key];
        }
        else
        {
            return false;
        }
    }
}

if(!function_exists('setUserSession()'))
{
    function setUserSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}

?>