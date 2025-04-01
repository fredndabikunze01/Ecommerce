<?php
require_once 'src/controller.php';
require_once 'src/view.php';
require_once 'src/model.php';
class Autoload{
    private $url = null;
    private $controller;
    public function __construct(){
        $this->getURL();
        $this->loadController();
    }

    private function getURL(){
        if(isset($_GET['req'])){
            $this->url = explode('/',$_GET['req']);
        }
    }

    private function loadDefaultController(){
        $filePath = 'app/controllers/'.DEFAULT_CONTROLLER.'.php';
        if(file_exists($filePath)){
            require_once $filePath;
            $className = DEFAULT_CONTROLLER;
            $this->controller = new $className;
            $this->controller->index();
        }
    }

    private function loadController(){
        if($this->url == null || empty($this->url)){
           
            $this->loadDefaultController();
            return 0;
        }
        $filePath = 'app/controllers/'.$this->url[0].".php";
        if(file_exists($filePath)){
            require_once $filePath;
            $this->controller = new $this->url[0]();
            $this->controller->index();
            
        }
        else{
            //print_r($this->url);
            echo "Error 404 Controller ".$this->url[0]." Not found";
        }
    }

    private function loadMethod(){
        if($this->controller <> null){
			if(!empty($this->url[1])){
				if(method_exists($this->controller,$this->url[1])){
					if(isset($this->_url[2])){
						$this->controller->{$this->url[1]}($this->url[2]);
					}
					else{
			
						if(isset($this->_url[1])){
							$this->controller->{$this->url[1]}();
						}
						// default should load index
						else{
							//load default controller
							if(!empty($this->url[0])){
						
								$this->controller->index();
							}
					
						}
					}
				}
				else
					$this->_methodNotFound();
			}
			else{
				if(method_exists($this->controller,'index'))
					$this->controller->index();
			}
        }
    }
}

?>