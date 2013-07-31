<?php

if(!function_exists('sanitize_number'))
{
    function sanitize_money($dirty_money)
    {
        $dirty_money_str = strval($dirty_money);
        $clean_money_str = str_replace(',', '', $dirty_money_str);

        return floatval($clean_money_str);
    }
}