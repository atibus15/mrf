<?php
// @Author  : atibus
// @Date    : 07/3/2013 last_upate 07/15/2013
// @Desc    : Load resources if needed este when it called.. 

class Loader
{

    protected $loaded_js;

    protected $loaded_css;

    protected $viewData = array();

    public function __set($key,$value)
    {
        $this->viewData[$key] = $value;
    }
    /*
     * @param $key : returns the value of the $key/index that is access in the $viewData
     * array;
     */
    public function __get($key)
    {
        if(array_key_exists($key, $this->viewData))
        {
            return $this->viewData[$key];
        }
    }

    public function model($model_name)
    {
        $file = 'application/model/'.$model_name.'.php';
        if(file_exists($file))
        {
            if(!include_once($file)) include_once($file);
            
            return new $model_name();
        }
        else
        {
            die('Error loading: model '.$model_name.' does not exist.');
        }
    }

    public function database($db_key)
    {
        return new Database($db_key);
    }

    public function helper($helper_name)
    {
        $file = UTI_HELPER.$helper_name.'.php';
        if(file_exists($file))
        {
            include_once($file);
        }
        else
        {
            die('Error loading: helper '.$helper_name.' does not exist.');
        }
    }

    public function library($library_name)
    {
        $file = LIBRARY_DIR.$library_name.'.php';

        if(file_exists($file))
        {
            include_once($file);
        }
        else
        {
            die('Error loading: library '.$file.' does not exist.');
        }
    }

    public function controller($controller_name)
    {
        $file = APPS_DIR."controller/".$controller_name.'.php';
        if(file_exists($file))
        {
            include_once($file);
            return new $controller_name();
        }
        else
        {
            die('Error loading: Controller '.$controller_name.' does not exist.');
        }
    }

    private function instantiateData($data)
    {
        foreach($data as $key=>$value)
        {
            $this->$key = $value;
        }
    }

    public function view($loaded_view='',$data=array())
    {
        if($data)
        {
            $this->instantiateData($data);
        }

        $view_page = APPS_DIR . "view/" .$loaded_view.".php";

        if(!is_file($view_page))
        {
            $not_found_view = "view/".$loaded_view;
            $view_page = APPS_DIR . "view/error/view_not_found.php";
        }

        foreach ($this->viewData as $key=>$value)
        {
            $key = $value;
        }

        include_once $view_page;
    }


    public function completeView($trans_view='', $data=array())
    {
        $this->view('template/header',$data);
        $this->view('template/menu',$data);
        $this->view($trans_view,$data);
        $this->view('template/footer',$data);
    }

    public function js($javascript='')
    {

        $javascript_file = root_dir($javascript).'.js';

        if(is_file($javascript_file))
        {
            $js_include_location = './'.$javascript.'.js';
            $this->loaded_js[] = '<script src="'.$js_include_location.'"></script>';
        }
    }

    public function css($stylesheet='')
    {
        $css_file = root_dir('styles/'.$stylesheet).'.css';
        if(is_file($css_file))
        {
            $css_location = './styles/'.$stylesheet.'.css';
            $this->loaded_css[] = '<link rel="stylesheet" type="text/css" href="'.$css_location.'" />';
        }
    }
}