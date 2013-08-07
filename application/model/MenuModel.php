<?php
// @Author  : atibus
// @Date    : 07/18/2013
// @Desc    : Menu Model
// @System  : e-Request
// @Dir     : /application/Model/MenuModel.php


if(!defined('ROOT_DIR'))exit('Direct access not allowed..');

class MenuModel extends Model
{
    private $user_role_tims;

    public function __construct()
    {
        $this->result['success'] = false;
        parent::__construct();

        $this->user_roleid_tims = userSession('roleidtims');

        $this->db = $this->load->database('HRMSDB');

        $this->db->setTrans(IBASE_READ);
    }

    // set MENU_NAME constant in /config/constant.php
    private function fetchMainMenus()
    {
        $query  =   "SELECT mm.MENUID, mm.MENUGROUP, mm.CAPTION, mm.ITEMPAGE, mm.ITEMACTION, rm.ROLEID FROM ADROLEMENU rm
                    JOIN ADMENUMAST mm ON mm.MENUID = rm.MENUID
                    WHERE rm.ROLEID = $this->user_roleid_tims  and mm.ITEMNAME like '".MENU_NAME."%' AND ITEMLEVEL = 1 AND mm.ISACTIVE = 1
                    ORDER BY mm.MENUID";

        $this->db->prepare($query);

        $this->db->execute();

        $main_menus = $this->db->fetchArray();

        return $main_menus;
    }
    // set MENU_NAME constant in /config/constant.php
    private function fetchSubMenus($menugroup)
    {
        $query = "SELECT mm.MENUID, mm.MENUGROUP, mm.CAPTION, mm.ITEMPAGE, mm.ITEMACTION,rm.ROLEID FROM ADROLEMENU rm
                    JOIN ADMENUMAST mm ON mm.MENUID = rm.MENUID
                    WHERE rm.ROLEID = $this->user_roleid_tims and mm.ITEMNAME like '".MENU_NAME."%' AND ITEMLEVEL = 2 AND mm.ISACTIVE = 1 AND mm.MENUGROUP = ?
                    ORDER BY mm.MENUID";

        $this->db->prepare($query);

        $this->db->execute($menugroup);

        $main_menus = $this->db->fetchArray();

        return $main_menus;
    }

    public function fetchMenus()
    {
        $main_nav = $this->fetchMainMenus();

        $main_menu_len = count($main_nav);

        for($i=0; $i<$main_menu_len; $i++)
        {
            $main_nav[$i]['sub_menus'] = $this->fetchSubMenus($main_nav[$i]['MENUID']);
        }
        return $main_nav;
    }

}