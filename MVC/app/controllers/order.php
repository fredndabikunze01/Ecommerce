<?php
require_once 'app/models/order_model.php';

class order extends Controller
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



public function insertOrder() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Get the raw JSON input from the request body
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true);

            // Validate JSON input
            if (!isset($data['cart']) || !isset($data['userId'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cart and user ID are required.'
                ]);
                exit();
            }

            $cart = $data['cart'];
            $userId = $data['userId'];

            // Start transaction
            $this->db->db->beginTransaction();

            // Insert each item in the cart into the customer_orders table
            foreach ($cart as $item) {
                // Validate that total_amount is provided
                if (!isset($item['total_amount'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Total amount is required for each item.'
                    ]);
                    exit();
                }

                $totalAmount = $item['total_amount'];

                // Insert into customer_orders table
                $sql = "INSERT INTO customer_orders (user_id, product_id, quantity, total_amount, created_by, status) 
                        VALUES (:user_id, :product_id, :quantity, :total_amount, :created_by, :status)";
                $stmt = $this->db->db->prepare($sql);
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':product_id', $item['product_id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':total_amount', $totalAmount);
                $stmt->bindParam(':created_by', $userId);
                $stmt->bindValue(':status', 'pending'); // Use bindValue for hardcoded values
                $stmt->execute();
            }

            // Commit transaction
            $this->db->db->commit();

            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully!'
            ]);
            exit();
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $this->db->db->rollBack();

            // Log the error for debugging
            error_log('Error: ' . $e->getMessage());

            // Return error response
            echo json_encode([
                'success' => false,
                'message' => 'Failed to place order. Please try again.'
            ]);
            exit();
        }
    } else {
        // Return error response for invalid request method
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);
        exit();
    }
}

    /**
     * Helper function to send JSON responses
     *
     * @param bool $success
     * @param string $message
     * @return void
     */
    private function sendResponse($success, $message) {
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit();
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


}