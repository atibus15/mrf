<?php
    
class UserModel extends Model
{

    public function __construct()
    {
        $this->result['success'] = false;
        parent::__construct();
    }

    public function getUserAccount($username='')
    {
        $this->db = $this->load->database('HRMSDB');

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


    public function fetchRole($user_id)
    {
     
        $this->db = $this->load->database('FINANCEDB');

        $this->db->setTrans(IBASE_READ);

         $query = "SELECT UR.ROLECODE, RM.ROLEDESC FROM USERROLE UR
                    INNER JOIN(SELECT ROLECODE, ROLEDESC, APPCODE, SEQNO, ISACTIVE FROM ADROLEMAST)RM ON RM.ROLECODE = UR.ROLECODE 
                    WHERE UR.USERID = ? AND RM.APPCODE = 'EREQUEST' AND UR.ISACTIVE = 1 AND RM.ISACTIVE = 1
                    ORDER BY RM.SEQNO ASC";


        $this->db->prepare($query);

        $this->db->execute($user_id);

        $user_role = $this->db->fetchArray();

        return $user_role;
    }

    public function getArrayCompiledRoleCode($user_id)
    {
        $compiled_role_code = array();
        $user_role = $this->fetchRole($user_id);

        $user_role_len = count($user_role);

        for($user_role_i = 0; $user_role_i < $user_role_len; $user_role_i++)
        {
            $compiled_role_code[] = $user_role[$user_role_i]['ROLECODE'];
        }

        return $compiled_role_code;
    }


}


?>