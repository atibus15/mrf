<?php

// @author : atibus
abstract class Controller{
    //put your code here

    /*
     * protected function to prevent instantiating
     * and to prevent creation of unknown Controller.
     */

    protected function  __construct() 
    {
        ;
    }

    /*
     * @param $page     : name of the page.
     * renders the page called at the frontcontroller
     * @param $action   : name of the action.
     * executes the proper action or method which process clients' request.
     */
    public function forward($page,$action)
    {
        $class = ucfirst($page);
        $exec_action = 'exec'.ucfirst($action);

        $file = APPS_DIR . "controller/" . $class . ".php";

        if(!is_file($file))
        {
            exit("Controller - Page not found");
        }

        include_once $file;

        $controller = new $class();

        $controller->setName($page);

        $controller->setAction($exec_action);
        
        $controller->dispatchAction();
    }
}
?>
