<?php
class count_admin_data extends Controller
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

   public function getProductCount() {
    try {
        // SQL query to count products
        $sql = "SELECT COUNT(product_id) as total FROM products;";

        // Execute the query
        $stmt = $this->db->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode(['total' => $result['total'] ?? 0]);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching product count: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}


   public function getOrderCount() {
    try {
        // SQL query to count products
        $sql = "SELECT COUNT(order_id) as total FROM customer_orders";

        // Execute the query
        $stmt = $this->db->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode(['total' => $result['total'] ?? 0]);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching product count: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}


   public function getCustomerCount() {
    try {
        // SQL query to count products
        $sql = "SELECT COUNT(user_id) as total FROM users where role_id='2'";

        // Execute the query
        $stmt = $this->db->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode(['total' => $result['total'] ?? 0]);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching product count: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

public function getRecentOrders() {
    try {
        // SQL query to get recent orders with customer and product info (exact query you provided)
        $sql = "SELECT o.order_id, u.username, p.product_name, o.total_amount, o.status 
                FROM customer_orders o 
                JOIN users u ON o.user_id = u.user_id 
                JOIN products p ON o.product_id = p.product_id 
                LIMIT 4";

        // Execute the query
        $stmt = $this->db->db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Error fetching recent orders: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

}