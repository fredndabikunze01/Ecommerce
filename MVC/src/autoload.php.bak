
<?php
/**
 * 
 */
require_once 'src/controller.php';
require_once 'src/view.php';
require_once 'src/model.php';
 final class Autoload{
    private $url =null;
    private $controller;
    public function __construct(){
       // echo "Now the auto load is about to start working<br />";
        if(isset($_GET['req'])){
            $this->url = explode('/',$_GET['req']);
        }

        if(!empty($this->url) && $this->url != null){
            $filePath = 'app/controllers/'.$this->url[0].".php";
            //echo $filePath;
            if(file_exists($filePath)){
                require_once $filePath;
                $this->controller = new $this->url[0];
                $this->controller->index();
            }
            else{
                echo "Error 404 Controller ".$this->url[0]." Not found";
            }
        }
        else{
            $filePath = 'app/controllers/'.DEFAULT_CONTROLLER.'.php';
            //echo $filePath;
            if(file_exists($filePath)){
                require_once $filePath;
                $className = DEFAULT_CONTROLLER;
                $this->controller = new $className;
                $this->controller->index();
            }
            else{
                echo "Error 404 Controller ".$this->url[0]." Not found";
            }
        }
        //print_r($this->url);
    }
 }
?>