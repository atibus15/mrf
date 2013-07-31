<?php
// @Author  : atibus
// @Date    : 07/18/2013
// @Desc    : Menu Model
// @System  : e-Request
// @Dir     : /application/Model/MenuModel.php


if(!defined('ROOT_DIR'))exit('Direct access not allowed..');

class MenuModel extends Model
{
    private $user_role_str;

    public function __construct()
    {
        $this->result['success'] = false;
        parent::__construct();

        $user_role_codes = userSession('roles');

        $this->user_role_str = "'".implode("','", $user_role_codes)."'";

        $this->db = $this->load->database('FINANCEDB');

        $this->db->setTrans(IBASE_READ);
    }

    public function fetchMainMenus()
    {
        $query = "SELECT DISTINCT mm.MENUID, mm.CAPTION, mm.ITEMLINK, mm.ITEMPAGE, mm.ITEMACTION FROM ADROLEMENU rm
                    inner join ADMENUMAST2 mm on rm.MENUID = mm.MENUID
                    WHERE rm.ROLECODE IN ({$this->user_role_str}) 
                    AND mm.ISACTIVE = 1 AND mm.ITEMNAME LIKE 'ereq%' AND mm.MENUGROUP = 0";

        $this->db->prepare($query);

        $this->db->execute();

        $main_menus = $this->db->fetchArray();

        return $main_menus;
    }

    public function fetchSubMenus($menugroup)
    {
        $query = "SELECT DISTINCT mm.MENUID, mm.CAPTION, mm.ITEMLINK, mm.ITEMPAGE, mm.ITEMACTION FROM ADROLEMENU rm
                    inner join ADMENUMAST2 mm on rm.MENUID = mm.MENUID
                    WHERE rm.ROLECODE IN ({$this->user_role_str}) 
                    AND mm.ISACTIVE = 1 AND mm.ITEMNAME LIKE 'ereq%' AND mm.MENUGROUP=?";

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