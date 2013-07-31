<?php

if(!function_exists('runtimeException'))
{
    function runtimeException($exception, $code=0)
    {
        throw new RuntimeException($exception, $code);
    }
}

?>