<?php
class count_user_data extends Controller
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



  

public function getallUserPayment() {
        try {
            session_start();
            $user_id = $_SESSION['user_id'];
            
            $sql = "SELECT o.order_id, u.username, p.product_name, o.total_amount, o.status 
                    FROM customer_orders o 
                    JOIN users u ON o.user_id = u.user_id 
                    JOIN products p ON o.product_id = p.product_id 
                    WHERE o.user_id = :user_id
                    ORDER BY o.created_at DESC
                    LIMIT 4";
            
            $stmt = $this->db->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Error fetching recent orders: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }




public function getRecentOrders() {
        try {
            session_start();
            $user_id = $_SESSION['user_id'];
            
            $sql = "SELECT o.order_id, u.username, p.product_name, o.total_amount, o.status 
                    FROM customer_orders o 
                    JOIN users u ON o.user_id = u.user_id 
                    JOIN products p ON o.product_id = p.product_id 
                    WHERE o.user_id = :user_id
                    ORDER BY o.created_at DESC
                    LIMIT 4";
            
            $stmt = $this->db->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Error fetching recent orders: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }


    public function getOrderCount() {
    try {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not authenticated");
        }

        $user_id = $_SESSION['user_id'];

        // SQL query to count orders for current user
        $sql = "SELECT COUNT(order_id) as total FROM customer_orders WHERE user_id = :user_id";
        
        // Prepare and execute the query with parameter binding
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode(['total' => $result['total'] ?? 0]);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching order count: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

    








}