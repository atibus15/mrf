<?php
if(!defined('ROOT_DIR'))exit('Direct access not allowed..');
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