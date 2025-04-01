<?php
require_once 'app/models/user_model.php';

class Register extends Controller {
    private $data = [];

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->view->render("register_view", $this->data);
    }

public function insert() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Create an instance of the UserModel class
            $model = new user_model();
            
            // Begin transaction
            $model->db->beginTransaction();
            
            // Prepare SQL statement for users table
            $sql = "INSERT INTO users (username, email, phone, role_id) VALUES (:username, :email, :phone, :role_id)";
            $stmt = $model->db->prepare($sql);
            
            // Bind parameters
            $stmt->bindParam(':username', $_POST['username']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':phone', $_POST['phone']);
            $stmt->bindParam(':role_id', $_POST['role_id']);
            
            // Execute the statement
            $stmt->execute();
            
            // Get the last inserted user ID
            $user_id = $model->db->lastInsertId();
            
            // Prepare SQL statement for password table
            $sql = "INSERT INTO passwords (password, created_at, user_id) VALUES (:password, NOW(), :user_id)";
            $stmt = $model->db->prepare($sql);
            
            // Bind parameters
            $stmt->bindParam(':password', $_POST['password']);
            $stmt->bindParam(':user_id', $user_id);
            
            // Execute the statement
            $stmt->execute();
            
            // Commit transaction
            $model->db->commit();
            
            if ($stmt->rowCount() > 0) {
                // Redirect to login page after successful registration
                header("Location: /Ecommerce/MVC/login");
                exit();
            } else {
                $this->data['error'] = "Failed to register. Please try again.";
                $this->view->render("register_view", $this->data);
            }
        } catch (PDOException $e) {
            // Rollback transaction in case of error
            $model->db->rollBack();
            $this->data['error'] = "Database error: " . $e->getMessage();
            $this->view->render("register_view", $this->data);
        }
    } else {
        $this->view->render("register_view", $this->data);
    }
}


}
?>


