<?php

if(!defined('ROOT_DIR')) exit('Direct access is not allowed.');

class Employee extends ActionController
{
    private $empmodel;

    private $ajax_result;
    public function __construct()
    {
        parent::__construct();
        $this->ajax_result['success'] = false;
    }

    // for ajax only;
    public function execGetEmployeeDetails()
    {
        $badgeno = get_post('badgeno');
        try
        {
            if(!$badgeno)
            {
                $this->ajax_result['errormsg'] = "Badge No. is required.";
                exit($this->buildJson( $this->ajax_result ));
            }

            $this->empmodel = $this->load->model('EmployeeModel');

            $emp_details = $this->empmodel->fetchEmployeeDetailsByBadgeNo($badgeno);
            if(!$emp_details)
            {
                runtimeException('Invalid Badge No.',1);
            }
            
            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $emp_details;
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }
}
