<?php
require_once 'app/models/order_model.php';

class orderpayment extends Controller
{
    private $db;
    private $data = [];

    public function __construct() {
        parent::__construct();

        // Initialize the database connection
        $this->db = new Model(); // Ensure Model is properly defined and imported
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // Redirect to login page if user is not logged in
            header("Location: /Ecommerce/MVC/login");
            exit();
        }

        $this->view->render("order_view", $this->data);
    }

        public function getOrdersWithUserDetails() {
    try {
        // SQL query to join users and customer_orders tables
        $sql = "SELECT 
    u.user_id, 
    u.username, 
    u.email, 
    u.phone, 
    u.create_at AS user_created_at, 
    u.updated_at AS user_updated_at, 
    u.status AS user_status, 
    o.order_id, 
    o.product_id, 
    p.product_name,  -- Added product name
    o.quantity, 
    o.total_amount, 
    o.status AS order_status, 
    o.created_by, 
    o.created_at AS order_created_at 
FROM 
    users u 
INNER JOIN 
    customer_orders o ON u.user_id = o.user_id
INNER JOIN
    products p ON o.product_id = p.product_id;  -- Added join to products table";

        // Execute the query
        $stmt = $this->db->db->query($sql);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode($orders);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching orders with user details: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}

public function getallPayment() {
        try {

             $userId = $_SESSION['user_id'];
            // SQL query to join users, customer_orders, and products tables
            $sql = "SELECT 
                        u.user_id, 
                        u.username, 
                        u.email, 
                        u.phone, 
                        u.create_at AS user_created_at, 
                        u.updated_at AS user_updated_at, 
                        u.status AS user_status, 
                        o.order_id, 
                        o.product_id, 
                        p.product_name,  -- Added product name
                        o.quantity, 
                        o.total_amount, 
                        o.status AS order_status, 
                        o.created_by, 
                        o.created_at AS order_created_at 
                    FROM 
                        users u 
                    INNER JOIN 
                        customer_orders o ON u.user_id = o.user_id
                    INNER JOIN
                        products p ON o.product_id = p.product_id
                         WHERE 
                    u.user_id = :user_id;  -- Added join to products table";

            // Execute the query
            $stmt = $this->db->db->query($sql);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the data as JSON
            header('Content-Type: application/json');
            echo json_encode($orders);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Error fetching payment details: " . $e->getMessage());

            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


}
