<?php
// @author : atibus
require_once 'Controller.php';
require_once 'ActionController.php';

class FrontController extends Controller{

    public function  __construct() 
    {
        $this->init();
    }
    /*
     * initializes common feateurs in web application.
*/
	public function init()
    {
		startSession();
	}
    /*
     * checks where APPS_DIR is defined or not.
     * APPS_DIR is a constant which calls the directory of the application.
     */
    public static function createInstance()
    {
        if(!defined('APPS_DIR'))
        {
            exit("Critical Error: Cannot proceed without APPS_DIR");
        }
        $instance = new self();
        return $instance;
    }


    /*
     * dispatches the proper page.
     * calls the controller object and
     * passes $page and $action for processing.
     */
    public function dispatch()
    {
        $page   = get_post('_page')   ? get_post('_page')   : DEFAULT_PAGE;
        $action = get_post('_action') ? get_post('_action') : DEFAULT_ACTION;

        $this->forward($page, $action);
    }
}
?>
