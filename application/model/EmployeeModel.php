<?php

class EmployeeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function fetchEmployeeDetailsByBadgeNo($badge_no)
    {
        $this->db = $this->load->database('HRMSDB');

        $this->db->setTrans(IBASE_READ);

        $query = "SELECT E.BADGENO,E.LASTNAME,E.FIRSTNAME,E.MIDDLENAME,E.NAME,E.NAMESUFFIX,
                        E.LOCATIONCODE,E.EMAILADDRESS,E.BUCODE, BU.SAPBUCODE, BU.BUDESC, E.DEPTCODE,E.GROUPCODE,
                        E.POSITIONCODE,E.HIREDATE,E.BRANCHCODE,E.BIRTHDATE ,E.DEPARTMENTCODE,
                        E.EMPSTATUSCODE, PS.POSITIONDESC, BR.BRANCHDESC, DP.DEPARTMENTDESC 
                    FROM EMPLOYEE E
                    INNER JOIN(SELECT P.POSITIONCODE, P.POSITIONDESC FROM LKPOSITION P)PS ON PS.POSITIONCODE=E.POSITIONCODE
                    INNER JOIN(SELECT B.BRANCHCODE, B.BRANCHDESC FROM LKBRANCH B)BR ON BR.BRANCHCODE = E.BRANCHCODE
                    INNER JOIN(SELECT D.DEPARTMENTCODE, D.DEPARTMENTDESC FROM LKDEPARTMENT D)DP ON DP.DEPARTMENTCODE = E.DEPARTMENTCODE
                    INNER JOIN(SELECT U.BUCODE, U.BUDESC, U.SAPBUCODE FROM LKBUSUNIT U)BU ON BU.BUCODE = E.BUCODE
                    WHERE E.BADGENO=?";

        $this->db->prepare($query);

        $this->db->execute($badge_no);

        $user_details = $this->db->fetchObject();

        return ($user_details) ? $user_details[0] : false;
    }

}