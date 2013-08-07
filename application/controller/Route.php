<?php

class Route extends ActionController
{
    private $ajax_result;

    public function __construct()
    {
        parent::__construct();

        $this->ajax_result['success'] = false;

        $this->requestmodel = $this->load->model('RequestModel');
        $this->employeemodel = $this->load->model('EmployeeModel');
        $this->notifier       = $this->load->controller('Notifier');
    }

    public function execUpdateRequest()
    {
        $this->new_status = get_post('action_code');
        $this->request_id = get_post('request_id');
        try
        {
            if(!$this->new_status or !$this->request_id)
            {
                $this->ajax_result['errormsg'] = 'Action code and EMRF ID are required.';
                exit( $this->buildJson($this->ajax_result) );
            }

            $this->requestmodel->updateEMRFRoute($this->new_status, $this->request_id);

            if($this->new_status == 'A')
            {
                $this->notifyNextApprover();
            }

            $this->requestmodel->commit();  
            $this->ajax_result['success'] = true;
            $this->ajax_result['message'] = 'Action succeeded.';         
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }


    private function notifyNextApprover()
    {

        $next_approver = $this->requestmodel->getNextApprover($this->request_id);

        if($next_approver)
        {
            $next_approver_badgeno  = $next_approver['badgeno'];

            $next_approver_roleid   = $next_approver['roleid'];

            $next_approver_info     = $this->employeemodel->fetchEmployeeDetailsByBadgeNo( $next_approver_badgeno );

            $next_approver_email_add = $next_approver_info->EMAILADDRESS;

            $this->notifier->sendMail($next_approver_email_add);

            return true;
        }
        else
        {
            return false;
        }
    }

}