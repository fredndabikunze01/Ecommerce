<?php
class customer extends Controller
{
    private $db;
    private $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->db = new Model(); // Use the Model class for database connection
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // Redirect to login page if user is not logged in
            header("Location: /Ecommerce/MVC/login");
            exit();
        }

        $this->view->render("customer_view", $this->data);
    }

   
public function getUsersWithRoles() {
    try {
        // SQL query to join users and roles tables
        $sql = "SELECT 
                    u.user_id, 
                    u.username, 
                    u.email, 
                    u.phone, 
                    u.create_at, 
                    u.updated_at, 
                    u.status, 
                    r.role_name 
                FROM 
                    users u
                LEFT JOIN 
                    roles r 
                ON 
                    u.role_id = r.role_id";

        // Execute the query
        $stmt = $this->db->db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode($users);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching users with roles: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}
}