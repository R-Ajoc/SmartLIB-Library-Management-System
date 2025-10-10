<?php

session_start(); 

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
        $this->user = new User($this->db);
    }

    // User login handler
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $result = $this->user->login($username, $password);

            if ($result['status']) {
                $userData = $result['data'];
                $role = $userData['role'];

                // session variables
                $_SESSION['userID'] = $userData['userID'];
                $_SESSION['role'] = $userData['role'];
                $_SESSION['firstname'] = $userData['firstname'] ?? '';
                $_SESSION['lastname'] = $userData['lastname'] ?? '';

                // Remember Me cookie
                if (!empty($_POST['remember'])) {
                    setcookie('username', $userData['username'], time() + (86400 * 7), "/"); // 7 days
                }

                // Redirect by role
                switch ($role) {
                    case 'student':
                        header("Location: ../view/dashboard_student.php");
                        break;
                    case 'teacher':
                        header("Location: ../view/dashboard_teacher.php");
                        break;
                    case 'librarian':
                        header("Location: ../view/lib_dashboard.php");
                        break;
                    case 'staff':
                        header("Location: ../view/dashboard_staff.php");
                        break;
                    default:
                        header("Location: ../view/login.php?error=Unknown role");
                        break;
                }
                exit;
            } else {
                header("Location: ../view/login.php?error=" . urlencode($result['message']));
                exit;
            }
        }
    }

    // sign up handler
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userID = trim($_POST['userID']);
            $email = trim($_POST['email']);
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $result = $this->user->signUp($userID, $email, $username, $password);

            if ($result['status']) {
                header("Location: ../view/login.php?success=" . urlencode($result['message']));
                exit;
            } else {
                header("Location: ../view/signup.php?error=" . urlencode($result['message']));
                exit;
            }
        }
    }

    // Logout handler
    public function logout() {
        session_start();
        session_unset();
        session_destroy();

        // delete cookie 
        if (isset($_COOKIE['username'])) {
            setcookie('username', '', time() - 3600, '/');
        }

        header("Location: ../view/login.php");
        exit;
    }

}

    // Routing Trigger
    if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
        echo "AuthController loaded successfully.<br>";
        $controller = new AuthController();

        if (isset($_GET['action']) && $_GET['action'] === 'login') {
            echo "Executing login()...<br>";
            $controller->login();
        } elseif (isset($_GET['action']) && $_GET['action'] === 'signup') {
            echo "Executing signup()...<br>";
            $controller->signup();
        } elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
            echo "Executing logout()...<br>";
            $controller->logout();
        } else {
            echo "No valid action found.";
        }
    }

?>
