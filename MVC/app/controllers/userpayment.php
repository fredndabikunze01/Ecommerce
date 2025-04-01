<?php
require_once 'app/models/order_model.php';

class userpayment extends Controller
{
    private $db;
    private $data = [];

    public function __construct()
    {
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

        $this->view->render("userpayment_view", $this->data);
    }

    public function getallPayment() {
        try {
            session_start();
            if (!isset($_SESSION['user_id'])) {
                // If the user is not logged in, return an error response
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized access. Please log in.']);
                exit();
            }

            // Get the logged-in user's ID from the session
            $userId = $_SESSION['user_id'];

            // SQL query to join users, customer_orders, products, and payments tables, filtered by user_id
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
                        o.created_at AS order_created_at, 
                        pay.payment_id, 
                        pay.amount AS payment_amount, 
                        pay.status AS payment_status, 
                        pay.create_at AS payment_created_at 
                    FROM 
                        users u 
                    INNER JOIN 
                        customer_orders o ON u.user_id = o.user_id
                    INNER JOIN
                        products p ON o.product_id = p.product_id
                    LEFT JOIN
                        payments pay ON o.order_id = pay.order_id
                    WHERE 
                        u.user_id = :user_id";

            // Prepare and execute the query
            $stmt = $this->db->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch the results
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the data as JSON
            header('Content-Type: application/json');
            echo json_encode($orders);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Error fetching payment details: " . $e->getMessage());

            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to fetch payment details. Please try again later.']);
        }
    }

}