<?php 
if(!defined('ROOT_DIR'))exit('Direct access not allowed..');

class User extends ActionController
{

    private $username;

    private $password;

    private $ajax_result;

    public function __construct()
    {
        parent::__construct();
        $this->ajax_result['success'] = false;
        $this->load->helper('output_sanitizer');

        $this->employeemodel = $this->load->model('EmployeeModel');
    }

    public function execLogout()
    {
        session_destroy();
        header("location: index.php?_page=user&_action=loginpage");
    }

    public function execLoginpage()
    {
        if(userSession('mrf')){$this->forward('user','homepage'); exit;}
        $this->load->css('login-style');
        $this->load->js('libraries/ext-4/ext-all');
        $this->load->js('scripts/js/login.min');
        $this->load->view('user/login');
    }

    public function execHomepage()
    {
        if(!userSession('mrf')){$this->forward('user','loginpage'); exit;}

        if(!userSession('serialized_user_menu'))
        {
            $this->setUserMenu();
            $this->setAccessibleModule();
        }

        $this->load->completeView('user/homepage');
    }

    // use in erequest login modoule
    public function execAuthenticateUser()
    {
        $this->username = post('username');
        $this->password = post('password');

        try
        {   
            if($this->isFormValid())
            {

                $user_account = $this->employeemodel->getUserAccount($this->username);

                if(!$user_account)
                {
                    throw new RuntimeException('Invalid username, please try again.',1);
                }
                else if($this->password != $user_account->USERPASSWD)
                {
                    throw new RuntimeException('Invalid password, please try again.',2);
                }
                else if($user_account->ISACTIVE==0)
                {
                    throw new RuntimeException('Your account is not anymore active, Please contact our H.R Department.',3);
                }
                else if($user_account->ACCTCREATED==0)
                {
                    throw new RuntimeException('Account not yet created. Please create an account to continue.',4);
                }
                else
                {

                    $emp_badge_no = $user_account->BADGENO;

                    $user_details = $this->employeemodel->fetchEmployeeDetailsByBadgeNo($emp_badge_no);
                    $user_details->USERID = $this->username;
                    
                    $this->setUserSession($user_details);

                    $this->ajax_result['success'] = true;

                    $this->ajax_result['page']['redirect'] = '?_page=user&_action=homepage';
                }
            }
        }
        catch(Exception $e)
        { 
            $this->ajax_result['errormsg'] = $e->getMessage();
        }
        echo json_encode($this->ajax_result);
    }



    private function setUserSession($user_details=array())
    {
        if($user_details)
        {
            setUserSession('mrf',true);
            
            foreach($user_details as $key => $value)
            {
                $session_key = strtolower($key);
                setUserSession($session_key, $value);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    private function isFormValid()
    {

        if(!$this->username)
        {
            throw new RuntimeException('Username is required.', 1);
        }
        else if(!$this->password)
        {
            throw new RuntimeException('Password is Required.',1);
        }
        else
        {
            return true;
        }
    }

    private function setUserMenu()
    {
        $this->menumodel = $this->load->model('MenuModel');

        $menus = $this->menumodel->fetchMenus();

        $serialized_user_menu = serialize($menus);

        setUserSession('serialized_user_menu', $serialized_user_menu);
    }

    private function setAccessibleModule()
    {
        $modules = unserialize(userSession('serialized_user_menu'));
        $user_module = array();
        foreach($modules as $mod)
        {
            $user_module[] = trim($mod['ITEMPAGE']).trim($mod['ITEMACTION']);

            foreach($mod['sub_menus'] as $mod_sub)
            {
                $user_module[] = trim($mod_sub['ITEMPAGE']).trim($mod_sub['ITEMACTION']);
            }
        }

        $serialized_user_module = serialize($user_module);
        setUserSession('serialized_user_module',$serialized_user_module);
    }
}

