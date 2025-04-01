<?php
class Welcome extends Controller{
    private $data = [];
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->view->render("intro_view", $this->data);
    }
}