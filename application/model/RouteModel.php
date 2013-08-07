<?php
if(!defined('ROOT_DIR')) exit('Direct access, not allowed.');

class RouteModel extends Model
{

    private $route_columns;

    public function __construct()
    {
        parent::__construct();

        $this->init();

        $this->hrmsdb = $this->load->database('HRMSDB');  

    }

    public function init()
    {
        $this->route_columns =  array(
            2=>array( 
                "ASSIGNEDROLE"  =>"ASSIGNEDSUPVR", 
                "FWDROLE"       =>"FWDEDTOSUPVR",
                "ROLESTATUS"    =>"SUPVRSTATUS",
                "STATUSBY"      =>"SUPVRSTATUSBY",
                "STATUSTIMESTAMP"=>"SUPVRSTATUSSTAMP",
                "FWDTIMESTAMP"  =>"FWDEDTOSUPVRSTAMP"),

            5=>array( 
                "ASSIGNEDROLE"  => "ASSIGNEDMGR", 
                "FWDROLE"       =>"FWDEDTOMGR",
                "ROLESTATUS"    =>"MGRSTATUS",
                "STATUSBY"      =>"MGRSTATUSBY",
                "STATUSTIMESTAMP"=>"MGRSTATUSSTAMP",
                "FWDTIMESTAMP"  =>"FWDEDTOMGRSTAMP"),

            3=>array( 
                "ASSIGNEDROLE"  =>"ASSIGNEDDHEAD", 
                "FWDROLE"       =>"FWDEDTODEPTHEAD",
                "ROLESTATUS"    =>"DEPTHEADSTATUS",
                "STATUSBY"      =>"DEPTHEADSTATUSBY",
                "STATUSTIMESTAMP"=>"DEPTHEADSTATUSSTAMP",
                "FWDTIMESTAMP"  =>"FWDEDTODEPTHEADSTAMP")
        );
    }



    public function fetchMRFApprovers($emrf_id)
    {
        $this->hrmsdb->setTrans(IBASE_READ);

        $query  =   "SELECT ASSIGNEDSUPVR, ASSIGNEDMGR, ASSIGNEDDHEAD FROM EMRFTRANROUTE WHERE TRANID = ?"; 

        $this->hrmsdb->prepare($query);

        $this->hrmsdb->execute($emrf_id);

        $mrf_approvers_arr = $this->hrmsdb->fetchArray();

        if($mrf_approvers_arr)
        {
            return $mrf_approvers_arr[0];
        }
        else
        {
            return false;
        }
    }

    //return array approver badgeno and roleid;
    public function getNextApprover($emrf_id)
    {
        $role_id = userSession('roleidtims');

        $approver = $this->fetchMRFApprovers($emrf_id);


        $sup_badgeno = trim($approver['ASSIGNEDSUPVR']);
        $mgr_badgeno = trim($approver['ASSIGNEDMGR']);
        $head_badgeno= trim($approver['ASSIGNEDDHEAD']);

        $next_approver = false;

        if($role_id == 1)
        {
            $next_approver = ($sup_badgeno) ? array('badgeno'=>$sup_badgeno,'roleid'=>2) : false; 
        }

        if($role_id == 2 and !$next_approver)
        {
            $next_approver = ($mgr_badgeno) ? array('badgeno'=>$mgr_badgeno,'roleid'=>5) : false;
        }

        if($role_id == 5 and !$next_approver)
        {
            $next_approver = ($head_badgeno) ? array('badgeno'=>$head_badgeno,'roleid'=>3) : false;
        }

        return $next_approver;
    }

    public function fetchRequest($request_status)
    {
        $role_tims = userSession('roleidtims');

        $approver_columns = $this->route_columns[ $role_tims ];

        $assigned_role  = $approver_columns['ASSIGNEDROLE'];
        $fwd_role       = $approver_columns['FWDROLE'];
        $role_status    = $approver_columns['ROLESTATUS'];
        $approver_badgeno = userSession('badgeno');

        $this->hrmsdb->setTrans(IBASE_READ);

        $query  =   "SELECT M.EMRFID,M.FILEDATE,M.BADGENO,M.LASTNAME,M.FIRSTNAME,M.MIDDLENAME,M.NAMESUFFIX,M.LASTNAME ||', '||M.FIRSTNAME||' '||M.MIDDLENAME AS FULLNAME,
                        M.HIREDATE,M.BUCODE,M.BUDESC,M.EMPSTATUSCODE,M.EMPSTATUSDESC,M.DEPARTMENTCODE,M.DEPARTMENTDESC,M.BRANCHCODE,M.BRANCHDESC,M.POSITIONCODE,M.POSITIONDESC,
                        M.EMPRANKCODE,M.EMPRANKDESC,M.MRFCOMPANY,M.MRFDEPT,M.NUMOFBODIES,M.POSITIONTYPE,M.MRFPOSITIONCODE,M.MRFRANK,M.DURATIONMOS,M.EMPLOYSTATUS,
                        M.REQREASONCODE,M.REPLACECODE,M.RELIEVERBADGENO,M.RELIEVERNAME,M.RELIEVERPOSITION,M.RELIEVERLOAFR,M.RELIEVERLOATO,M.RELIEVERLOAREASON,
                        M.ISINPLANTILLA,M.DESCRIBEJUSTIFYTEXT,M.JOBDESC,M.EDUCATTAINED,M.EDUCPREFERRED,M.WORKEXPERIENCE,M.SKILLSREQ,M.MACHINESKILLS,M.SOFTWARESKILLS,
                        M.OTHERQUALS,M.AGEFROM,M.AGETO,M.GENDER,M.SALARYRANGE,M.REMARKS,M.ISAPPROVED,M.APPROVEDBY,M.APPROVEDDATE,M.CANDIDATEHIREDATE,M.ENDORSEDDATE,
                        M.CANDIDATENAME, BU.MRFBUDESC, D.MRFDEPARTMENTDESC, P.MRFPOSITIONDESC, G.MRFRANKDESC, G2.MRFREASONDESC, G3.REPLACEREASON,
                        G4.ATTAINMENTDESC, G5.SALARYDESC
                        /*
                        ,R.ASSIGNEDSUPVR,R.ASSIGNEDMGR,R.ASSIGNEDDHEAD,R.FWDEDTOSUPVR,R.FWDEDTOSUPVRSTAMP,R.SUPVRSTATUS,R.SUPVRSTATUSSTAMP,R.SUPVRSTATUSBY,R.FWDEDTOMGR,
                        R.FWDEDTOMGRSTAMP,R.MGRSTATUS,R.MGRSTATUSSTAMP,R.MGRSTATUSBY,R.FWDEDTODEPTHEAD,R.FWDEDTODEPTHEADSTAMP,R.DEPTHEADSTATUS,
                        R.DEPTHEADSTATUSSTAMP,R.DEPTHEADSTATUSBY*/
                    FROM EMRF M 
                    JOIN EMRFTRANROUTE R ON R.TRANID = M.EMRFID
                    JOIN (SELECT BUCODE, BUDESC AS MRFBUDESC FROM LKBUSUNIT)BU ON BU.BUCODE = M.MRFCOMPANY
                    JOIN (SELECT DEPARTMENTCODE, DEPARTMENTDESC AS MRFDEPARTMENTDESC FROM LKDEPARTMENT)D ON D.DEPARTMENTCODE = M.MRFDEPT
                    JOIN (SELECT POSITIONCODE, POSITIONDESC AS MRFPOSITIONDESC FROM LKPOSITION)P ON P.POSITIONCODE = M.MRFPOSITIONCODE
                    JOIN (SELECT LKCODE, DESCRIPTION AS MRFRANKDESC FROM LKGENERIC)G ON G.LKCODE = M.MRFRANK
                    JOIN (SELECT LKCODE, DESCRIPTION AS MRFREASONDESC FROM LKGENERIC)G2 ON G2.LKCODE = M.REQREASONCODE
                    LEFT JOIN (SELECT LKCODE, DESCRIPTION AS REPLACEREASON FROM LKGENERIC)G3 ON G3.LKCODE = M.REPLACECODE
                    JOIN (SELECT LKCODE, DESCRIPTION AS ATTAINMENTDESC FROM LKGENERIC)G4 ON G4.LKCODE = M.EDUCATTAINED
                    JOIN (SELECT LKCODE, DESCRIPTION AS SALARYDESC FROM LKGENERIC)G5 ON G5.LKCODE = M.SALARYRANGE
                    
                    WHERE R.{$assigned_role}='{$approver_badgeno}' AND R.{$fwd_role} = 1 AND R.{$role_status} = ?";

        $this->hrmsdb->prepare($query);
        $this->hrmsdb->execute($request_status);

        $request_list_arr = $this->hrmsdb->fetchArray();

        return $request_list_arr;
    }

    public function updateEMRFRoute($new_status, $request_id)
    {
        $role_tims              = userSession('roleidtims');
        $userid                 = userSession('userid');
        $approver_columns       = $this->route_columns[ $role_tims ];
        $status_column          = $approver_columns['ROLESTATUS'];
        $approver_name_column   = $approver_columns['STATUSBY'];
        $status_time_column     = $approver_columns['STATUSTIMESTAMP'];

        $query  =   "UPDATE EMRFTRANROUTE SET {$status_column}=?, {$approver_name_column}=?, {$status_time_column}=CURRENT_TIMESTAMP,
                    LASTUPDATEDATE=CURRENT_TIMESTAMP, LASTUPDATEBY='{$user_id}', WHERE TRANID = ?";
        $this->hrmsdb->prepare($query);
        $this->hrmsdb->execute(array($new_status, $userid, $request_id));
        return $this;
    }

    public function forwardToNextApprover($new_approver_role_id, $request_id)
    {
        $next_approver_columns  = $this->route_columns[ $new_approver_role_id ];
        $forward_column         = $next_approver_columns['FWDROLE'];
        $forward_time_columns   = $next_approver_columns['FWDTIMESTAMP'];

        $query  =   "UPDATE EMRFTRANROUTE  SET {$forward_column}=1, {$forward_time_columns}=CURRENT_TIMESTAMP,
                    LASTUPDATEDATE=CURRENT_TIMESTAMP, LASTUPDATEBY='{$user_id}' 
                    WHERE TRANID = ?";
        $this->hrmsdb->prepare($query);
        $this->hrmsdb->prepare($request_id);
        return $this;
    }

    public function fineshRoute($request_id)
    {
        $user_id=userSession('userid');
        $query = "UPDATE EMRF SET ISFINISHEDROUTE = 1, LASTUPDATEDATE=CURRENT_TIMESTAMP, LASTUPDATEBY='{$user_id}' WHERE EMRFID=?";
        $this->hrmsdb->prepare($query);
        $this->hrmsdb->execute($request_id);
        return $this;
    }

    public function getRouteColumns()
    {
        return $this->route_columns;
    }       

    public function commit()
    {
        $this->hrmsdb->commitTrans();
    }

    public function rollback()
    {
        $this->hrmsdb->rollbackTrans();
    }             
}