<?php 

class LookUpModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('HRMSDB');
        $this->db->setTrans(IBASE_READ);
    }

    public function fetchCompanies()
    {

        $query  = "SELECT BUCODE, BUDESC FROM LKBUSUNIT WHERE BUSTYPE in (1, 2, 3, 4, 5) ORDER BY BUDESC";

        $this->db->prepare($query);
        $this->db->execute();

        $company_arr = $this->db->fetchArray();
        return $company_arr;
    }

    public function fetchDepartments()
    {
        $query  = "SELECT DEPARTMENTCODE, DEPARTMENTDESC FROM LKDEPARTMENT WHERE SEQNOMLIST IS NOT NULL ORDER BY SEQNOMLIST";

        $this->db->prepare($query);
        $this->db->execute();

        $department_arr = $this->db->fetchArray();
        return $department_arr;
    }

    public function fetchPositions()
    {        
        $query  =   "SELECT POSITIONCODE, POSITIONDESC FROM LKPOSITION ";
        $query  .=  "ORDER BY POSITIONDESC";

        $this->db->prepare($query);
        $this->db->execute();

        $position_arr = $this->db->fetchArray();
        return $position_arr;
    }

    public function fetchJobDescByPositionCode($position_code)
    {
        $query  =   "SELECT REMARKS FROM STPRFPART WHERE PARTTYPE = 1 AND POSITIONCODE = ? AND ISACTIVE = 1
        ORDER BY SEQNO";
        $this->db->prepare($query);
        $this->db->execute($position_code);

        $job_desc_arr = $this->db->fetchArray();

        return $job_desc_arr;
    }


    public function fetchGeneric($app_code, $sub_code)
    {
        $query  =   "SELECT LKCODE, DESCRIPTION FROM LKGENERIC
                    WHERE APPCODE = ? AND SUBAPPCODE = ? AND ISACTIVE = 1
                    ORDER BY SEQNO";

        $this->db->prepare($query);
        $this->db->execute(array($app_code,$sub_code));

        $generic_look_up = $this->db->fetchArray();
        return $generic_look_up;
    }

    public function fetchRequirements()
    {
        $this->db->setTrans(IBASE_READ);

        $query  =   "SELECT STREQUIREID, DESCRIPTION, ISREQUIRED FROM STREQUIRE WHERE APPCODE = 'EMRF' AND SUBAPPCODE='GENERIC' AND ISACTIVE=1 ORDER BY SEQNO ASC";

        $this->db->prepare($query);
        $this->db->execute();

        $requirements_arr = $this->db->fetchArray();

        return $requirements_arr; 
    }


}