<?php
// @author  : atibus
// @date    : 07/18/2013
// @desc    : load this view if you need to display common user info in javascript pages;  
// @Ex.     : Ext.get('userid').getValue(); or $('#userid').val()
    
    $this->helper('output_sanitizer');

    $user_info = $_SESSION;

    unset($user_info['user_menu']);
    unset($user_info['user_module']);

    $clean_user_info = sanitizeOutput($user_info);

    foreach($clean_user_info as $key => $value)
    {
        echo "<input type='hidden' id='{$key}' value='{$value}' />"."\n";
    }

?>