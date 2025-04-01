<?php
require_once 'app/models/user_model.php';

class Login extends Controller {
    private $data = [];

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->view->render("login_view", $this->data);
    }

public function auth() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Create an instance of the UserModel class
            $model = new user_model();

            // Prepare SQL statement to fetch user and their role
            $sql = "SELECT u.user_id, u.email, r.role_name 
                    FROM users u
                    LEFT JOIN roles r ON u.role_id = r.role_id
                    WHERE u.email = :email";
            $stmt = $model->db->prepare($sql);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();

            // Fetch user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Prepare SQL statement to fetch password
                $sql = "SELECT password FROM passwords WHERE user_id = :user_id";
                $stmt = $model->db->prepare($sql);
                $stmt->bindParam(':user_id', $user['user_id']);
                $stmt->execute();

                // Fetch password data
                $passwordData = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify password
                if ($passwordData && $_POST['password'] === $passwordData['password']) {
                    // Start session and set user data
                    session_start();
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    

                    // Redirect based on role
                    if ($user['role_name'] === 'admin') {
                        header("Location: /Ecommerce/MVC/alldatadashboard");
                    } else if ($user['role_name'] === 'customer') {
                        header("Location: /Ecommerce/MVC/userdashboard");
                    } else {
                        // Default redirect if role is not recognized
                        header("Location: /Ecommerce/MVC/login");
                    }
                    exit();
                } else {
                    $this->data['error'] = "Invalid email or password.";
                    $this->view->render("login_view", $this->data);
                }
            } else {
                $this->data['error'] = "Invalid email or password.";
                $this->view->render("login_view", $this->data);
            }
        } catch (PDOException $e) {
            $this->data['error'] = "Database error: " . $e->getMessage();
            $this->view->render("login_view", $this->data);
        }
    } else {
        $this->view->render("login_view", $this->data);
    }
}

      public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /Ecommerce/MVC/");
        exit();
    }


    public function verifyuser() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Get the raw JSON input from the request body
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true);

            // Validate JSON input
            if (!isset($data['username']) || !isset($data['password'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Username and password are required.'
                ]);
                exit();
            }

            // Extract username and password from JSON
            $username = $data['username'];
            $password = $data['password'];

            // Create an instance of the UserModel class
            $model = new user_model();

            // Prepare SQL statement to fetch user and password
            $sql = "SELECT u.user_id, p.password 
                    FROM users u
                    JOIN passwords p ON u.user_id = p.user_id
                    WHERE u.email = :username";
            $stmt = $model->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // Fetch user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify password (plain text comparison)
                if ($password === $user['password']) {
                    // Return success response
                    echo json_encode([
                        'success' => true,
                        'userId' => $user['user_id'],
                        'message' => 'Login successful.'
                    ]);
                    exit();
                } else {
                    // Return error response for invalid password
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid username or password.'
                    ]);
                    exit();
                }
            } else {
                // Return error response for invalid username
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid username or password.'
                ]);
                exit();
            }
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('Database error: ' . $e->getMessage());

            // Return error response for database errors
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
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
}
?>

