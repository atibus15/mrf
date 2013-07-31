<?php
// @author : atibus 
// last update :07/13/2013
abstract class ActionController extends Controller{
    //put your code here

    /*
     * name of page to be displayed.
     * filename of the view file must be equal to
     * the controllers method name.
     */
    protected $name;

    protected $load;

    protected $action;

    protected $data = array();
    /*
     * protected constructor prevents instantiating.
     * protected constructor to prevent creation
     * of unknown ActionController.
     */
    protected function  __construct() 
    {
        $this->load = new Loader();
    }

    public function __set($key,$value)
    {
        $this->data[$key] = $value;
    }
    /*
     * @param $key : returns the value of the $key/index that is access in the $viewData
     * array;
     */
    public function __get($key)
    {
        if(array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }
    }

    /*
     * the data passed to the view.
     * ex: $this->hello = "Hello World!"
     * view layer knows the value of the key 'hello' and displays it upong call.
     */

    /*
     * @param $name: passed parameter from the fron controller
     * $_GET['page'] or the complete directory of the view page.
     */
    public function setName($new_name)
    {
        $this->name = $new_name;
    }
    /*
     * returns the directory of the view page.
     * and it ready the page to be included in the basetemplate.
     */
    public function getName()
    {
        return $this->name;
    }
    /*
     * @param $key : the array key or the index of the variable where
     *               view page accesses to displage in the view layer.
     * @param $value: the value of the key or the index in the array.
     *
     * Ex. $this->hello = "Hello World"
     * $key = hello
     * $value = "Hello World"
     * the data in the array($viewData) looks like this after inserting the key hello
     * $viewData = array('hello'=>"Hello World");
     */



    public function setAction($new_action)
    {
        $this->action = $new_action;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }
    
    public function buildJson($params)
    {
       return json_encode($params);
    }

    public function dispatchAction()
    {
        $method = $this->getAction();

        if(!method_exists($this, $method)){
            exit("Page not found!");
        }
        $this->$method();
    }
}
?>
