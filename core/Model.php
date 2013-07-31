<?php

abstract class Model
{
    protected $load;

    protected $db;

    public function __construct()
    {
        $database = UTI_CORE.'Database.php';
        if(!include_once($database)) include_once $database;

        $this->load = new Loader();
    }
}