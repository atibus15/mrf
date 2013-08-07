<?php
if(!defined('ROOT_DIR'))exit('Direct access not allowed..');

class Request extends ActionController
{
    private $ajax_result;


    public function __construct()
    {
        $this->ajax_result['success'] = false;

        if($this->isAjaxRequest() and !userSession('mrf'))
        {
            $this->ajax_result['errormsg'] = 'Session expired, please relogin.';
            exit($this->buildJson( $this->ajax_result ));
        }
        
        if(!userSession('mrf'))
        {
            $this->forward('user','loginpage'); exit;
        }

        parent::__construct();

        $this->requestmodel     = $this->load->model('RequestModel');
    }


    public function execRequisitionForm()
    {
        $this->checkUserPrivilege();
        $data['form_title'] = 'Manpower Requisition';
        $this->load->css('ext-bootstrap-fix-conflict');
        $this->load->js('scripts/js/common-form-functions');
        $this->load->js('scripts/js/manpower');
        // $this->load->js('scripts/js/manpower.min');
        $this->load->completeView('request/generic_view',$data);
    }



    public function execMRFProcessing()
    {
        $this->checkUserPrivilege();
        $data['form_title'] = 'Manpower Requisition';
        $this->load->css('ext-bootstrap-fix-conflict');
        $this->load->js('scripts/js/common-form-functions');
        $this->load->js('scripts/js/request-processing');
        $this->load->completeView('request/generic_view',$data);
    }

    



    public function execGetRequestList()
    {
        try
        {
            $status     = get_post('status');
            $date_start = date('m/d/Y',strtotime(get_post('date_start')));
            $date_end   = date('m/d/Y',strtotime(get_post('date_end')));
            //paging
            $limit      = get_post('limit');
            $start      = get_post('start');
            $skip  = $limit * $start; 

            $formatted_list = array();

            $request_list = $this->requestmodel->fetchRequest($status, $date_start, $date_end, $limit, $skip);

            $total_request = $this->requestmodel->fetchTotalRequest($status,$date_start, $date_end);

            $role_status_column_name = $this->requestmodel->getRoleStatusColumnName();

            foreach($request_list as $record_arr)
            {
                $formatted_list[] = array(
                        $record_arr['EMRFID'],
                        date('m/d/Y',strtotime($record_arr['FILEDATE'])),
                        $record_arr['BADGENO'],
                        $record_arr['LASTNAME'],
                        $record_arr['FIRSTNAME'],
                        $record_arr['MIDDLENAME'],
                        $record_arr['NAMESUFFIX'],
                        $record_arr['FULLNAME'],
                        $record_arr['HIREDATE'],
                        $record_arr['BUCODE'],
                        $record_arr['BUDESC'],
                        $record_arr['EMPSTATUSCODE'],
                        $record_arr['EMPSTATUSDESC'],
                        $record_arr['DEPARTMENTCODE'],
                        $record_arr['DEPARTMENTDESC'],
                        $record_arr['BRANCHCODE'],
                        $record_arr['BRANCHDESC'],
                        $record_arr['POSITIONCODE'],
                        $record_arr['POSITIONDESC'],
                        $record_arr['EMPRANKCODE'],
                        $record_arr['EMPRANKDESC'],
                        $record_arr['MRFCOMPANY'],
                        $record_arr['MRFDEPT'],
                        $record_arr['NUMOFBODIES'],
                        $record_arr['POSITIONTYPE'],
                        $record_arr['MRFPOSITIONCODE'],
                        $record_arr['MRFRANK'],
                        $record_arr['DURATIONMOS'],
                        $record_arr['EMPLOYSTATUS'],
                        $record_arr['REQREASONCODE'],
                        $record_arr['REPLACECODE'],
                        $record_arr['RELIEVERBADGENO'],
                        $record_arr['RELIEVERNAME'],
                        $record_arr['RELIEVERPOSITION'],
                        $record_arr['RELIEVERLOAFR'],
                        $record_arr['RELIEVERLOATO'],
                        $record_arr['RELIEVERLOAREASON'],
                        $record_arr['ISINPLANTILLA'],
                        $record_arr['DESCRIBEJUSTIFYTEXT'],
                        $record_arr['JOBDESC'],
                        $record_arr['EDUCATTAINED'],
                        $record_arr['EDUCPREFERRED'],
                        $record_arr['WORKEXPERIENCE'],
                        $record_arr['SKILLSREQ'],
                        $record_arr['MACHINESKILLS'],
                        $record_arr['SOFTWARESKILLS'],
                        $record_arr['OTHERQUALS'],
                        $record_arr['AGEFROM'],
                        $record_arr['AGETO'],
                        $record_arr['GENDER'],
                        $record_arr['SALARYRANGE'],
                        $record_arr['REMARKS'],
                        $record_arr['ISAPPROVED'],
                        $record_arr['APPROVEDBY'],
                        $record_arr['APPROVEDDATE'],
                        $record_arr['CANDIDATEHIREDATE'],
                        $record_arr['ENDORSEDDATE'],
                        $record_arr['CANDIDATENAME'],
                        $record_arr['MRFBUDESC'],
                        $record_arr['MRFDEPARTMENTDESC'],
                        $record_arr['MRFPOSITIONDESC'],
                        $record_arr['MRFRANKDESC'],
                        $record_arr['MRFREASONDESC'],
                        $record_arr['REPLACEREASON'],
                        $record_arr['ATTAINMENTDESC'],
                        $record_arr['SALARYDESC'],
                        $record_arr[$role_status_column_name]
                    );
            }
            

            $this->ajax_result['success'] = true;
            $this->ajax_result['data'] = $formatted_list;
            $this->ajax_result['totalrequest'] = $total_request;
        }
        catch(Exception $e)
        {
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }



    public function execSendMRF()
    {
        try
        {
            $this->validateMRFData();
            $this->initMRFData();

            $this->employeemodel    = $this->load->model('EmployeeModel');
            $this->notifier         = $this->load->controller('Notifier');

            $this->requestmodel->setMRFDetail($this->mrf_data);

            $this->requestmodel->insertMRF();

            $request_id = $this->requestmodel->getRequestID();

            if(http_file())
            {
                $this->uploadRequirements('EMRF','GENERIC', $request_id);
            }

            $next_approver = $this->requestmodel->getNextApprover($request_id);

            if($next_approver)
            {
                $next_approver_badgeno  = $next_approver['badgeno'];

                $next_approver_roleid   = $next_approver['roleid'];

                $next_approver_info     = $this->employeemodel->fetchEmployeeDetailsByBadgeNo( $next_approver_badgeno );

                $next_approver_email_add = $next_approver_info->EMAILADDRESS;

                $this->notifier->sendMail($next_approver_email_add);
            }

            $this->requestmodel->commit();

            $this->ajax_result['success'] = true;

            $this->ajax_result['message'] = 'Request succeeded.';
        }
        catch(Exception $e)
        {
            $this->requestmodel->rollback();
            $this->ajax_result['errormsg'] = "System error. Request Terminated.";
            $this->load->helper('Logger');
            Logger::write($e);
        }

        echo $this->buildJson( $this->ajax_result );
    }



    private function uploadRequirements($app_code, $sub_app_code, $new_dir)
    {
        $files = array();

        $this->lookupmodel = $this->load->model('LookUpModel'); 

        $requirements_arr = $this->lookupmodel->fetchRequirements($app_code,$sub_app_code);


        foreach($requirements_arr as $req)
        {
            if($req['ISREQUIRED'] and !http_file($req['STREQUIREID']))
            {
                // rollback db transaction and exit process;
                $this->requestmodel->rollbackRequest();
                $this->ajax_result['errormsg'] = $req->DESCRIPTION." is required.";
                exit($this->buildJson( $this->ajax_result ));
            }

            $files[] = array('id'=>$req['STREQUIREID'],'description'=>$req['DESCRIPTION']);
        }
        
        $this->load->library('Uploader');

        $uploader = new Uploader();

        $uploader->setFiles( $files );
        $uploader->setAppCode( $app_code );
        $uploader->setNewDirectory( $new_dir );
        $uploaded = $uploader->saveAttachement();
        
        if(!$uploaded)
        {
            $this->requestmodel->rollback();
            
            $this->ajax_result['errormsg'] = $uploader->getMessage();

            exit( $this->buildJson($this->ajax_result) );
        }

        $uploaded_files = $uploader->getFiles();

        foreach($uploaded_files as $file)
        {
            $file_detail = array(
                    $file['id'], 
                    $file['newfilename'],
                    userSession('userid')
                );
            
            $this->requestmodel->insertRequirements($file_detail);
        }
        return true;
    }



    private function validateMRFData()
    {
        foreach(post() as $key=>$value)
        {
            if($value=='')
            {
                $field_label = str_replace('_', ' ', $key);

                $this->ajax_result['errormsg'] = ucwords($field_label).' is required.';

                exit($this->buildJson( $this->ajax_result )); return;
            }
        }
    }



    private function initMRFData()
    {
        $this->mrf_data = array(
            post('filedate'),
            userSession('badgeno'), 
            userSession('lastname'), 
            userSession('firstname'), 
            userSession('middlename'), 
            userSession('namesuffix'), 
            userSession('hiredate'), 
            userSession('bucode'), 
            userSession('budesc'), 
            userSession('empstatuscode'), 
            userSession('empstatusdesc'), 
            userSession('departmentcode'), 
            userSession('departmentdesc'), 
            userSession('branchcode'), 
            userSession('branchdesc'), 
            userSession('positioncode'), 
            userSession('positiondesc'), 
            userSession('emprankcode'), 
            userSession('emprankdesc'), 
            post('company'), 
            post('department'), 
            post('needed_number'), 
            post('position_type'), 
            post('position_title'), 
            post('rank'), 
            post('duration_of_contract'), 
            post('employment_status'), 
            post('reason_of_request'), 
            post('replacement_to'), 
            post('employee_badge_no'), 
            post('employee_name'), 
            post('employee_position'), 
            post('loa_from'), 
            post('loa_to'), 
            post('extended_leave_reason'), 
            post('within_platilla'), 
            post('justification'), 
            post('job_description'), 
            post('education_attainment'), 
            post('preferred_education'), 
            post('work_experience'), 
            post('required_skills'), 
            post('machine_skill'), 
            post('software_skill'), 
            post('other_qualification'), 
            post('age_from'), 
            post('age_to'), 
            post('gender'), 
            post('salary_range'), 
            post('userid'),
        );
    }


}