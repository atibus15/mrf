<?php
if(!defined('ROOT_DIR')) exit('Direct access, not allowed.');

class RequestModel extends Model
{
    private $mrf_details_arr;

    private $request_id;

    private $route_columns;

    public function __construct()
    {
        parent::__construct();

        $this->init();

        $this->hrmsdb = $this->load->database('HRMSDB');   
        $this->hrmsdb->setTrans(IBASE_DEFAULT);
    }

    public function init()
    {
        $this->user_id = userSession('userid');
        $this->role_id_tims = userSession('roleidtims');
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


    public function insertMRF()
    {
        

        $query  =   "INSERT INTO EMRF( FILEDATE,BADGENO, LASTNAME, FIRSTNAME, MIDDLENAME, NAMESUFFIX, HIREDATE, BUCODE, BUDESC, EMPSTATUSCODE, EMPSTATUSDESC, 
                    DEPARTMENTCODE, DEPARTMENTDESC, BRANCHCODE, BRANCHDESC, POSITIONCODE, POSITIONDESC, EMPRANKCODE, EMPRANKDESC, MRFCOMPANY, MRFDEPT, 
                    NUMOFBODIES, POSITIONTYPE, MRFPOSITIONCODE, MRFRANK, DURATIONMOS, EMPLOYSTATUS, REQREASONCODE, REPLACECODE, RELIEVERBADGENO, RELIEVERNAME, 
                    RELIEVERPOSITION, RELIEVERLOAFR, RELIEVERLOATO, RELIEVERLOAREASON, ISINPLANTILLA, DESCRIBEJUSTIFYTEXT, JOBDESC, EDUCATTAINED, EDUCPREFERRED, 
                    WORKEXPERIENCE, SKILLSREQ, MACHINESKILLS, SOFTWARESKILLS, OTHERQUALS, AGEFROM, AGETO, GENDER, SALARYRANGE, CREATEDDATE, CREATEDBY)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP,?)";

        $this->hrmsdb->prepare($query);

        $this->hrmsdb->execute($this->mrf_details_arr);

        $this->request_id = $this->hrmsdb->gen_id('EMRF_GEN',0);
    }

    public function insertRequirements($requirement_dtl=array())
    {
        
        $query  =   "INSERT INTO EMRFREQUIRE
                            (HEADERID, STREQUIREID, FILENAME, CREATEDDATE, CREATEDBY)
                    VALUES({$this->request_id},?,?,CURRENT_TIMESTAMP,?)";

        $this->hrmsdb->prepare($query);

        $this->hrmsdb->execute($requirement_dtl);
    }


    public function fetchMRFApprovers($emrf_id)
    {

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


    public function fetchRequest($request_status,$date_start, $date_end, $limit=10, $skip=0)
    {
        $approver_columns = $this->route_columns[ $this->role_id_tims ];

        $assigned_role      = $approver_columns['ASSIGNEDROLE'];
        $fwd_role           = $approver_columns['FWDROLE'];
        $this->role_status  = $approver_columns['ROLESTATUS'];
        $approver_badgeno   = userSession('badgeno');

        $this->hrmsdb->setTrans(IBASE_READ);

        $query  =   "SELECT FIRST {$limit} SKIP {$skip} M.EMRFID,M.FILEDATE,M.BADGENO,M.LASTNAME,M.FIRSTNAME,M.MIDDLENAME,M.NAMESUFFIX,M.LASTNAME ||', '||M.FIRSTNAME||' '||M.MIDDLENAME AS FULLNAME,
                        M.HIREDATE,M.BUCODE,M.BUDESC,M.EMPSTATUSCODE,M.EMPSTATUSDESC,M.DEPARTMENTCODE,M.DEPARTMENTDESC,M.BRANCHCODE,M.BRANCHDESC,M.POSITIONCODE,M.POSITIONDESC,
                        M.EMPRANKCODE,M.EMPRANKDESC,M.MRFCOMPANY,M.MRFDEPT,M.NUMOFBODIES,M.POSITIONTYPE,M.MRFPOSITIONCODE,M.MRFRANK,M.DURATIONMOS,M.EMPLOYSTATUS,
                        M.REQREASONCODE,M.REPLACECODE,M.RELIEVERBADGENO,M.RELIEVERNAME,M.RELIEVERPOSITION,M.RELIEVERLOAFR,M.RELIEVERLOATO,M.RELIEVERLOAREASON,
                        M.ISINPLANTILLA,M.DESCRIBEJUSTIFYTEXT,M.JOBDESC,M.EDUCATTAINED,M.EDUCPREFERRED,M.WORKEXPERIENCE,M.SKILLSREQ,M.MACHINESKILLS,M.SOFTWARESKILLS,
                        M.OTHERQUALS,M.AGEFROM,M.AGETO,M.GENDER,M.SALARYRANGE,M.REMARKS,M.ISAPPROVED,M.APPROVEDBY,M.APPROVEDDATE,M.CANDIDATEHIREDATE,M.ENDORSEDDATE,
                        M.CANDIDATENAME, BU.MRFBUDESC, D.MRFDEPARTMENTDESC, P.MRFPOSITIONDESC, G.MRFRANKDESC, G2.MRFREASONDESC, G3.REPLACEREASON,
                        G4.ATTAINMENTDESC, G5.SALARYDESC, R.{$this->role_status}
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
                    
                    WHERE R.{$assigned_role}='{$approver_badgeno}' AND R.{$fwd_role} = 1 AND R.{$this->role_status} = ?
                    AND M.FILEDATE BETWEEN '{$date_start}' AND '{$date_end}'
                    ORDER BY M.FILEDATE DESC";

        $this->hrmsdb->prepare($query);
        $this->hrmsdb->execute($request_status);

        $request_list_arr = $this->hrmsdb->fetchArray();

        return $request_list_arr;
    }


    public function fetchTotalRequest($request_status,$date_start, $date_end)
    {
        $approver_columns = $this->route_columns[ $this->role_id_tims ];

        $assigned_role  = $approver_columns['ASSIGNEDROLE'];
        $fwd_role       = $approver_columns['FWDROLE'];
        $this->role_status= $approver_columns['ROLESTATUS'];
        $approver_badgeno = userSession('badgeno');

        $this->hrmsdb->setTrans(IBASE_READ);

        $query  =   "SELECT COUNT(M.EMRFID) AS TOTALREQUEST FROM EMRF M JOIN EMRFTRANROUTE R ON R.TRANID = M.EMRFID
                    WHERE R.{$assigned_role}='{$approver_badgeno}' AND R.{$fwd_role} = 1 AND R.{$this->role_status} = ?
                    AND M.FILEDATE BETWEEN '{$date_start}' AND '{$date_end}'";

        $this->hrmsdb->prepare($query);
        $this->hrmsdb->execute($request_status);

        $data = $this->hrmsdb->fetchArray();

        return $data[0]['TOTALREQUEST'];
    }


    public function updateEMRFRoute($new_status, $request_id)
    {

        $approver_columns       = $this->route_columns[ $this->role_id_tims ];
        $status_column          = $approver_columns['ROLESTATUS'];
        $approver_name_column   = $approver_columns['STATUSBY'];
        $status_time_column     = $approver_columns['STATUSTIMESTAMP'];

        $query  =   "UPDATE EMRFTRANROUTE SET {$status_column}=?, {$approver_name_column}=?, {$status_time_column}=CURRENT_TIMESTAMP,
                    LASTUPDATEDATE=CURRENT_TIMESTAMP, LASTUPDATEBY='{$this->user_id}' WHERE TRANID = ?";

        $this->hrmsdb->prepare($query);

        $this->hrmsdb->execute(array($new_status, $this->user_id, $request_id));

        return $this;
    }


    public function forwardToNextApprover($new_approver_role_id, $request_id)
    {
        $next_approver_columns  = $this->route_columns[ $new_approver_role_id ];
        $forward_column         = $next_approver_columns['FWDROLE'];
        $forward_time_columns   = $next_approver_columns['FWDTIMESTAMP'];

        $query  =   "UPDATE EMRFTRANROUTE  SET {$forward_column}=1, {$forward_time_columns}=CURRENT_TIMESTAMP,
                    LASTUPDATEDATE=CURRENT_TIMESTAMP, LASTUPDATEBY='{$this->user_id}' 
                    WHERE TRANID = ?";

        $this->hrmsdb->prepare($query);

        $this->hrmsdb->prepare($request_id);

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

    public function setMRFDetail($new_mrf_details=array())
    {
        $this->mrf_details_arr = $new_mrf_details;
        return $this;
    }

    public function getMRFDetail()
    {
        return $this->mrf_details_arr;
    }

    public function getRequestID()
    {
        return $this->request_id;
    }

    public function getRoleStatusColumnName(){
        return $this->role_status;
    }
}