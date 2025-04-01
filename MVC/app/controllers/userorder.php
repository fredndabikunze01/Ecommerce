<?php
require_once 'app/models/order_model.php';

class userorder extends Controller
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
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['email'])) {
            // Redirect to login page if user is not logged in
            header("Location: /Ecommerce/MVC/login");
            exit();
        }

        $this->view->render("userorder_view", $this->data);
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
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // If the user is not logged in, return an error response
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized access. Please log in.']);
            exit();
        }

        // Get the logged-in user's ID and email from the session
        $userId = $_SESSION['user_id'];
        $email = $_SESSION['email'];

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
                    p.product_name, 
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
                    u.user_id = :user_id AND u.email = :email"; // Filter by user_id and email

        // Prepare and execute the query
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the results
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




public function deleteOrder($orderId) {
    try {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // If the user is not logged in, return an error response
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized access. Please log in.']);
            exit();
        }

        // Get the logged-in user's ID from the session
        $userId = $_SESSION['user_id'];

        // Start transaction
        $this->db->db->beginTransaction();

        // Check if the order belongs to the logged-in user
        $sql = "SELECT order_id FROM customer_orders WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            // If no matching order is found, return an error response
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Order not found or you do not have permission to delete this order.']);
            exit();
        }

        // Delete the order
        $sql = "DELETE FROM customer_orders WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaction
        $this->db->db->commit();

        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully.']);
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $this->db->db->rollBack();

        // Log the error for debugging
        error_log("Error deleting order: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to delete order. Please try again.']);
    }
}



public function makePayment($orderId) {
    try {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // If the user is not logged in, return an error response
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized access. Please log in.']);
            exit();
        }

        // Get the logged-in user's ID
        $userId = $_SESSION['user_id'];

        // Start transaction
        $this->db->db->beginTransaction();

        // Validate the order and fetch order details
        $sql = "SELECT o.order_id, o.product_id, o.quantity AS order_quantity, o.total_amount, 
                       i.quantity AS stock_quantity, i.unit_price
                FROM customer_orders o
                INNER JOIN in_stock i ON o.product_id = i.product_id
                WHERE o.order_id = :order_id AND o.user_id = :user_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            // If the order does not exist or does not belong to the user
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Order not found or you do not have permission to make payment for this order.']);
            exit();
        }

        // Check stock availability
        $orderQuantity = $order['order_quantity'];
        $stockQuantity = $order['stock_quantity'];

        if ($orderQuantity > $stockQuantity) {
            // If the order quantity is greater than the available stock
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Insufficient stock for this order.']);
            exit();
        } elseif ($orderQuantity == $stockQuantity) {
            // If the order quantity is equal to the available stock, subtract all
            $newStockQuantity = 0;
        } else {
            // If the order quantity is less than the available stock, subtract the order quantity
            $newStockQuantity = $stockQuantity - $orderQuantity;
        }

        // Insert payment record
        $sql = "INSERT INTO payments (order_id, amount, created_by, status) 
                VALUES (:order_id, :amount, :created_by, :status)";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':order_id', $order['order_id'], PDO::PARAM_INT);
        $stmt->bindParam(':amount', $order['total_amount'], PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':status', 'completed', PDO::PARAM_STR);
        $stmt->execute();

        // Update stock in the in_stock table
        $sql = "UPDATE in_stock SET quantity = :quantity WHERE product_id = :product_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':quantity', $newStockQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $order['product_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Insert record into out_stock table
        $sql = "INSERT INTO out_stock (product_id, quantity, user_id) 
                VALUES (:product_id, :quantity, :user_id)";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':product_id', $order['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $orderQuantity, PDO::PARAM_INT);
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Update the order status to completed
        $sql = "UPDATE customer_orders SET status = :status WHERE order_id = :order_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindValue(':status', 'completed', PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $order['order_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaction
        $this->db->db->commit();

        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Payment completed successfully.']);
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $this->db->db->rollBack();

        // Log the error for debugging
        error_log("Error making payment: " . $e->getMessage());

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to complete payment. Please try again.']);
    }
}




}