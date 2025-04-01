<?php
require_once 'src/model.php';

class productcreate extends Controller {
    private $db;
    private $data = [];

    public function __construct() {
        parent::__construct();
        $this->db = new Model(); // Use the Model class for database connection
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            // Redirect to login page if user is not logged in
            header("Location: /Ecommerce/MVC/login");
            exit();
        }

        $this->view->render("productcreate_view", $this->data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Handle image upload
                $imagePath = null;
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/products/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    // Generate unique filename
                    $fileExtension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
                    $uniqueFilename = uniqid('product_') . '.' . $fileExtension;
                    $imagePath = $uploadDir . $uniqueFilename;

                    if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $imagePath)) {
                        throw new Exception("Failed to upload image");
                    }
                }

                // Start transaction
                $this->db->db->beginTransaction();

                // Insert product into the products table
                $sql = "INSERT INTO products (product_name, user_id, created_at, updated_at, status, description, image_path) 
                        VALUES (:product_name, :user_id, NOW(), NOW(), :status, :description, :image_path)";
                $stmt = $this->db->db->prepare($sql);
                $stmt->bindParam(':product_name', $_POST['product_name']);
                $stmt->bindParam(':user_id', $_SESSION['user_id']); // Assuming user_id is stored in the session
                $stmt->bindParam(':status', $_POST['status']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->bindParam(':image_path', $imagePath);
                $stmt->execute();

                // Get the last inserted product ID
                $product_id = $this->db->db->lastInsertId();

                // Insert stock information into the in_stock table
                $sql = "INSERT INTO in_stock (product_id, quantity, unit_price, created_at) 
                        VALUES (:product_id, :quantity, :unit_price, NOW())";
                $stmt = $this->db->db->prepare($sql);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity', $_POST['quantity']);
                $stmt->bindParam(':unit_price', $_POST['price']);
                $stmt->execute();

                // Commit transaction
                $this->db->db->commit();

                // Redirect or return success response
                header("Location: /Ecommerce/MVC/productcreate");
                exit();
            } catch (Exception $e) {
                // Rollback transaction in case of error
                $this->db->db->rollBack();
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Invalid request method.";
        }
    }

 


public function getAllProducts() {
    try {
        $sql = "SELECT 
                    p.product_id, 
                    p.product_name, 
                    p.user_id, 
                    p.created_at, 
                    p.updated_at, 
                    p.status, 
                    p.description, 
                    p.image_path, 
                    s.quantity, 
                    s.unit_price 
                FROM 
                    products p
                LEFT JOIN 
                    in_stock s 
                ON 
                    p.product_id = s.product_id";

        // Log the query for debugging
        error_log("Executing SQL: " . $sql);

        $stmt = $this->db->db->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Log the fetched data
        error_log("Fetched Products: " . print_r($products, true));

        header('Content-Type: application/json');
        echo json_encode($products);
    } catch (Exception $e) {
        // Log the error
        error_log("Error fetching products: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}


public function delete($productId) {
    try {
        // Start transaction
        $this->db->db->beginTransaction();

        // Delete from in_stock table
        $sql = "DELETE FROM in_stock WHERE product_id = :product_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete from products table
        $sql = "DELETE FROM products WHERE product_id = :product_id";
        $stmt = $this->db->db->prepare($sql);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaction
        $this->db->db->commit();

        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $this->db->db->rollBack();
        error_log("Error deleting product: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

public function update() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Handle image upload if a new image is provided
            $imagePath = $_POST['existing_image_path']; // Default to existing image path
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename
                $fileExtension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
                $uniqueFilename = uniqid('product_') . '.' . $fileExtension;
                $imagePath = $uploadDir . $uniqueFilename;

                if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $imagePath)) {
                    throw new Exception("Failed to upload image");
                }
            }

            // Start transaction
            $this->db->db->beginTransaction();

            // Update product in the products table
            $sql = "UPDATE products 
                    SET product_name = :product_name, 
                        status = :status, 
                        description = :description, 
                        image_path = :image_path, 
                        updated_at = NOW() 
                    WHERE product_id = :product_id";
            $stmt = $this->db->db->prepare($sql);
            $stmt->bindParam(':product_name', $_POST['product_name']);
            $stmt->bindParam(':status', $_POST['status']);
            $stmt->bindParam(':description', $_POST['description']);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->bindParam(':product_id', $_POST['product_id'], PDO::PARAM_INT);
            $stmt->execute();

            // Update stock information in the in_stock table
            $sql = "UPDATE in_stock 
                    SET quantity = :quantity, 
                        unit_price = :unit_price 
                    WHERE product_id = :product_id";
            $stmt = $this->db->db->prepare($sql);
            $stmt->bindParam(':quantity', $_POST['quantity']);
            $stmt->bindParam(':unit_price', $_POST['price']);
            $stmt->bindParam(':product_id', $_POST['product_id'], PDO::PARAM_INT);
            $stmt->execute();

            // Commit transaction
            $this->db->db->commit();

            // Return success response
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $this->db->db->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo "Invalid request method.";
    }
}




}
?>