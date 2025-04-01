<?php
require_once 'app/models/user_model.php';

class alldatadashboard extends Controller
{
    private $data = [];

    public function __construct()
    {
        parent::__construct();
    }






    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // Redirect to login page if user is not logged in
            header("Location: /Ecommerce/MVC/login");
            exit();
        }

        $this->view->render("alldatadashboard_view", $this->data);
    }



  


}