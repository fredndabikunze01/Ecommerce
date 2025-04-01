<?php
class Error extends Controller{
	function __construct(){
		parent::__construct();
		
	}
	
	public function index(){
		//echo("This is an error.<br />");
		$data['msg'] = "404 This page does not exist";
		$this->view->render("error/index",$data);
	}
	
	public function methodNotFound($class,$method){
		$data['msg'] = "The Class <strong>".$class."</strong> does not contain the method <strong>".$method."</strong>";
		$this->view->render("error/index",$data);
	}
}
