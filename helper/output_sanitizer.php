<?php
/**
 *@Author : atibus
 *@date : 06/13/2013
 *Description : Output helper.. 
 **/ 

if(!function_exists('sanitizeOutput'))
{
    function sanitizeOutput($dirty_value)
    {
        $clean_output = '';
        if($dirty_value) :

            if(is_array($dirty_value))
            {
                foreach($dirty_value as $key => $value)
                {
                    if(!is_array($value)) $clean_output[$key] =  trim($value);
                }
                
            }
            else if(is_object($dirty_value))
            {
                foreach($dirty_value as $key => $value)
                {
                    if(!is_array($value)) $clean_output->$key = trim($value);
                }
            }
            else
            {
                $clean_output = trim($$dirty_value);
            }
        endif;
        return $clean_output;
    }
}
?>