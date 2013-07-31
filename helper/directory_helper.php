<?php
/**
 *@Author : atibus
 *@date : 06/13/2013
 *Description : Directory helper.. 
 **/ 

if(!function_exists('root_dir'))
{
    function root_dir($appended_dir='')
    {
        return realpath(__DIR__.'/../').'/'.$appended_dir;
    }
}
?>