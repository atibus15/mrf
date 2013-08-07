<?php
if(!defined('ROOT_DIR'))exit('Direct access not allowed..');
class LookUp extends ActionController
{
    private $ajax_result;

    public function __construct()
    {
        parent::__construct();
        $this->ajax_result['success'] = false;

        if($this->isAjaxRequest() and !userSession('mrf'))
        {
            $this->ajax_result['errormsg'] = 'Session expired, please relogin.';
            exit($this->buildJson( $this->ajax_result ));
        }
    }

    public function execGetCompanies()
    {
        try
        {
            $companies_final = array();

            $this->lookupmodel = $this->load->model('LookUpModel');
            
            $company_dtl_arr = $this->lookupmodel->fetchCompanies();

            foreach($company_dtl_arr as $company)
            {
                $companies_final[] = array(
                    'code'=>trim($company['BUCODE']),
                    'desc'=>trim($company['BUDESC'])
                    );
            }

            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $companies_final;            
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }

    public function execGetDepartments()
    {
        try
        {
            $departments_final = array();
            $this->lookupmodel = $this->load->model('LookUpModel');
            $departments_dtl_arr = $this->lookupmodel->fetchDepartments();

            foreach($departments_dtl_arr as $department)
            {
                $departments_final[] = array(
                    'code'=>trim($department['DEPARTMENTCODE']),
                    'desc'=>trim($department['DEPARTMENTDESC'])
                    );
            }

            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $departments_final;            
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }

    public function execGetPositions()
    {
        try
        {
            $position_final = array();
            
            $this->lookupmodel = $this->load->model('LookUpModel');

            $position_dtl_arr = $this->lookupmodel->fetchPositions();

            foreach($position_dtl_arr as $position)
            {
                $position_final[] = array(
                    'code'=>trim($position['POSITIONCODE']),
                    'desc'=>trim($position['POSITIONDESC'])
                    );
            }

            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $position_final;            
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }


    public function execGetGenericLooKUp()
    {
        try
        {
            $app_code = get_post('app_code');
            $sub_code = get_post('sub_code');

            if(!$app_code or !$sub_code)
            {
                $this->ajax_result['errormsg'] = 'Application and Sub Application code required.';
                exit($this->buildJson($this->ajax_result));
            }

            $this->lookupmodel = $this->load->model('LookUpModel');

            $lookup_final = array();
            $lookup_arr = $this->lookupmodel->fetchGeneric($app_code, $sub_code);

            foreach($lookup_arr as $lookup)
            {
                $lookup_final[] = array(
                    'code'=>trim($lookup['LKCODE']),
                    'desc'=>trim($lookup['DESCRIPTION'])
                    );
            }

            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $lookup_final;            
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }

    public function execGetJobDescriptions()
    {
        try
        {
            
            $position_code = get_post('position_code');
            $formatted_job_desc='';
            if(!$position_code)
            {
                $this->ajax_result['errormsg'] = 'Position code is required.';
                exit( $this->buildJson( $this->ajax_result) );
            }

            $this->lookupmodel = $this->load->model('LookUpModel');

            $job_desc_arr = $this->lookupmodel->fetchJobDescByPositionCode($position_code);

            foreach($job_desc_arr as $key => $job)
            {
                $num = $key + 1;
                $formatted_job_desc .= $num .'. ) '.$job['REMARKS']."\n\r";
            }
            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $formatted_job_desc;
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }
        echo $this->buildJson( $this->ajax_result );
    }

    public function execGetRequirements()
    {
        try
        {
            $this->lookupmodel = $this->load->model('LookUpModel');

            $requirement_arr    = $this->lookupmodel->fetchRequirements();

            $this->ajax_result['success'] = true;

            $this->ajax_result['data'] = $requirement_arr;
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