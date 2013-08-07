<?php
if(!defined('ROOT_DIR'))exit('Direct access not allowed..');
class EmployeeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('HRMSDB');
    }


    public function fetchEmployeeDetailsByBadgeNo($badge_no)
    {
        
        $this->db->setTrans(IBASE_READ);

        $query = "SELECT E.BADGENO, E.LASTNAME, E.FIRSTNAME, E.MIDDLENAME, E.NAMESUFFIX, E.NAME, E.LOCATIONCODE,
                    E.BIRTHDATE, E.HIREDATE,E.EMAILADDRESS, E.BUCODE, E.DEPTCODE, E.GROUPCODE, E.POSITIONCODE, E.BRANCHCODE,
                    E.DEPARTMENTCODE, E.COSTCENTERCODE, E.EMPSTATUSCODE, E.EMPRANKCODE, E.GENDER,
                    E.ROLEIDTIMS, PS.POSITIONDESC, BR.BRANCHDESC, DP.DEPARTMENTDESC,BU.SAPBUCODE, BU.BUDESC, R.EMPRANKDESC, S.EMPSTATUSDESC
                    FROM EMPLOYEE E
                    INNER JOIN(SELECT P.POSITIONCODE, P.POSITIONDESC FROM LKPOSITION P)PS ON PS.POSITIONCODE=E.POSITIONCODE
                    INNER JOIN(SELECT B.BRANCHCODE, B.BRANCHDESC FROM LKBRANCH B)BR ON BR.BRANCHCODE = E.BRANCHCODE
                    INNER JOIN(SELECT D.DEPARTMENTCODE, D.DEPARTMENTDESC FROM LKDEPARTMENT D)DP ON DP.DEPARTMENTCODE = E.DEPARTMENTCODE
                    INNER JOIN(SELECT U.BUCODE, U.BUDESC, U.SAPBUCODE FROM LKBUSUNIT U)BU ON BU.BUCODE = E.BUCODE
                    INNER JOIN(SELECT EMPRANKCODE, EMPRANKDESC FROM LKEMPRANK)R ON R.EMPRANKCODE = E.EMPRANKCODE
                    INNER JOIN(SELECT EMPSTATUSCODE, EMPSTATUSDESC FROM LKEMPSTATUS)S ON S.EMPSTATUSCODE = E.EMPSTATUSCODE
                    WHERE E.BADGENO=?";

        $this->db->prepare($query);

        $this->db->execute($badge_no);

        $user_details = $this->db->fetchObject();

        return ($user_details) ? $user_details[0] : false;
    }

    public function getUserAccount($username='')
    {

        $this->db->setTrans(IBASE_READ);

        $query = "SELECT A.USERID, A.ISACTIVE,A.USERPASSWD,A.USERACCTID, B.BADGENO,C.ACCTCREATED FROM USERACCT A
                    INNER JOIN (SELECT S.USERACCTID,S.BADGENO FROM STEMPUSERACCT S)B ON B.USERACCTID=A.USERACCTID
                    INNER JOIN (SELECT UI.BADGENO,UI.REFERENCEIDNO,UI.ACCTCREATED FROM STUSERINIT UI)C ON B.BADGENO=C.BADGENO 
                    WHERE A.USERID=?";

        $this->db->prepare($query);

        $this->db->execute($username);

        $user_account = $this->db->fetchObject();

        return ($user_account) ? $user_account[0] : false;
    }

}