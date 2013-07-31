<?php

class ErrorPage extends ActionController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execForbidden()
    {
        $this->load->view('error/forbidden');
    }
}