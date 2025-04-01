<?php
/*@fileName: bootstrap.php
 *@author: Noah Nambale [namnoah@gmail.com]
 *@date: 2025-03-01 
 *
 */
require_once 'src/controller.php';
require_once 'src/view.php';
require_once 'src/model.php';
class Autoload {
	private $_url = null;
	private $_controller = null;
	//private $_error = new Error();
	function __construct(){
/**
 *get the set URL
 */
		$this->_getURL();
		if(empty($this->_url[0])){
			$this->_loadDefaultController();
		}
		$this->_loadController();

/**
 *load control method if called
 *@param $this->_url[2]
 */
		$this->_loadControllerMethod();
	}
	
	private function _getURL(){
		if(isset($_GET['req'])){
            $this->_url = explode('/',$_GET['req']);
        }
	}
	
	private function _loadDefaultController(){
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
            return 0;
	}
	
	private function _loadController(){
		if(!empty($this->_url[0])){
			$file = 'app/controllers/'.$this->_url[0].'.php';
		
			if(file_exists($file)){
				require_once($file);
			
			}
			else{
				$this->_fileNotFoundError();
				//echo("file ".$file." not found");
				return false;
			 	//throw new Exception("The file ".$file." does not exist");
			}
			$this->_controller = new $this->_url[0];
			
		}
	}
	
	
	
	private function _loadControllerMethod(){
		if($this->_controller <> null){
			if(!empty($this->_url[1])){
				if(method_exists($this->_controller,$this->_url[1])){
					if(isset($this->_url[2])){
						$this->_controller->{$this->_url[1]}($this->_url[2]);
					}
					else{
			
						if(isset($this->_url[1])){
							$this->_controller->{$this->_url[1]}();
						}
						// default should load index
						else{
							//load default controller
							if(!empty($this->_url[0])){
						
								$this->_controller->index();
							}
					
						}
					}
				}
				else
					$this->_methodNotFound();
			}
			else{
				if(method_exists($this->_controller,'index'))
					$this->_controller->index();
			}
		}
	}
	
	private function _fileNotFoundError(){
		
		if(file_exists("app/controllers/error.php")){
			require_once("app/controllers/error.php");
			$error = new Error();

			$error->index($this->_url[0] );
			
		}
		else{
			echo("Error, page ".$this->_url[0]." Not found");
		}
	}
	
	private function _methodNotFound(){
		if(file_exists("app/controllers/error.php")){
			require_once("app/controllers/error.php");
			$error = new Error();
			$error->methodNotFound($this->_url[0], $this->_url[1]);
		}
		else{
			echo("Error, Class ".$this->_url[0]." does not contain ".$this->_url[1]." Method");
		}
	}
}
?>

