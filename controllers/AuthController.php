<?php
session_start();

require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../config/Database.php';

$root_path = '../'; // one level up

$authModel = new AuthModel();

// Make sure action is set
if (!isset($_GET['action'])) {
    header("Location: ../views/login.php");
    exit();
}

// Redirect helper
function redirect($path, $type, $message) {
    $_SESSION[$type] = $message;
    header("Location: " . $path);
    exit();
}


// Logout handling
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    
    session_unset();
    session_destroy();
    
    if (isset($_COOKIE['remember_me_token'])) {
        setcookie('remember_me_token', '', time() - 3600, "/");
    }
    
    header("Location: {$root_path}views/login.php");
    exit();
}


// Profile page handling
if (isset($_GET['action']) && $_GET['action'] === 'profile') {

    if (!isset($_SESSION['is_logged_in'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $authModel = new AuthModel();
    $userId = $_SESSION['user_id'];
    $user = $authModel->getUserById($userId);

    if (!$user) {
        die("Unable to load profile.");
    }

    include '../modals/profile.php';
    exit();
}


// Post request handing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {

    $action = $_GET['action'];
    $authModel = new AuthModel();

    // Signup logic
    if ($action === 'signup') {

        $errors = [];

        $required_fields = ['firstname', 'lastname', 'email', 'username', 'password', 'password_confirm', 'role'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst($field) . " is required.";
            }
        }

        $firstname = sanitizeInput($_POST['firstname']);
        $midint = sanitizeInput($_POST['midint'] ?? '');
        $lastname = sanitizeInput($_POST['lastname']);
        $email = sanitizeInput($_POST['email']);
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $role = sanitizeInput($_POST['role']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if ($password !== $password_confirm) {
            $errors[] = "Passwords do not match.";
        }

        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }

        if (empty($errors)) {

            if ($authModel->userExists($username, $email)) {
                $errors[] = "Username or Email already registered.";
            } else {

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $userData = [
                    'firstname' => $firstname,
                    'midint' => $midint,
                    'lastname' => $lastname,
                    'email' => $email,
                    'username' => $username,
                    'password' => $hashed_password,
                    'role' => $role
                ];

                if ($authModel->registerUser($userData)) {
                    redirect("../views/login.php", 'success', 'Registration successful! You may now log in.');
                } else {
                    $errors[] = "Registration failed due to a server error.";
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header("Location: ../views/signup.php");
            exit();
        }
    }


    // Login logic
    if ($action === 'login') {

        $errors = [];

        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        $remember_me = isset($_POST['remember_me']);

        if (empty($username) || empty($password)) {
            $errors[] = "Username and Password are required.";
        }

        if (empty($errors)) {

            $user = $authModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_logged_in'] = true;
                $_SESSION['firstname'] = $user['firstname'];

                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + (86400 * 30);

                    setcookie('remember_me_token', $token, $expiry, "/");
                    $authModel->updateRememberToken($user['user_id'], $token);
                }

                switch ($user['role']) {
                    case 'student':
                        header("Location: ../views/student/dashboard.php");
                        break;
                    case 'teacher':
                        header("Location: ../views/teacher/dashboard.php");
                        break;
                    case 'librarian':
                        header("Location: ../views/librarian/dashboard.php");
                        break;
                    case 'staff':
                        header("Location: ../views/staff/staff_main.php");
                        break;
                    default:
                        header("Location: ../views/general/dashboard.php");
                        break;
                }

                exit();
            }

            $errors[] = "Invalid username or password.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = ['username' => $username];
            header("Location: ../views/login.php");
            exit();
        }
    }


    if ($action === 'update_profile') {
        $userId = intval($_POST['user_id']);
        $firstname = sanitizeInput($_POST['firstname']);
        $midint = sanitizeInput($_POST['midint'] ?? '');
        $lastname = sanitizeInput($_POST['lastname']);
        $email = sanitizeInput($_POST['email']);
        $username = sanitizeInput($_POST['username']);

        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($firstname) || empty($lastname) || empty($username)) {
            $errors[] = "All fields are required.";
        }

        if (empty($errors)) {
            $updated = $authModel->updateUserProfile($userId, [
                'firstname' => $firstname,
                'midint' => $midint,
                'lastname' => $lastname,
                'email' => $email,
                'username' => $username,
            ]);

            if ($updated) {
                // Redirect to role-specific settings page
                switch ($_SESSION['role']) {
                    case 'librarian':
                        $redirectPath = '../views/librarian/settings.php';
                        break;
                    case 'student':
                        $redirectPath = '../views/student/student_setting.php';
                        break;
                    case 'teacher':
                        $redirectPath = '../views/teacher/teacher_setting.php';
                        break;
                    case 'staff':
                        $redirectPath = '../views/staff/staff_setting.php';
                        break;
                    default:
                        $redirectPath = '../views/login.php';
                        break;
                }

                redirect($redirectPath, 'success', 'Profile updated successfully.');
            } else {
                $errors[] = "Update failed. Try again.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        }
    }

    // Change password logic
    if ($action === 'change_password') {
        $userId = intval($_POST['user_id']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $errors = [];

        $user = $authModel->getUserById($userId);
        if (!$user || !password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }

        if (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        }

        if (empty($errors)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $updated = $authModel->updateUserPassword($userId, $hashed);

            if ($updated) {
                // Redirect to role-specific settings page
                switch ($_SESSION['role']) {
                    case 'librarian':
                        $redirectPath = '../views/librarian/setting.php';
                        break;
                    case 'student':
                        $redirectPath = '../views/student/student_setting.php';
                        break;
                    case 'teacher':
                        $redirectPath = '../views/teacher/teacher_setting.php';
                        break;
                    case 'staff':
                        $redirectPath = '../views/staff/staff_setting.php';
                        break;
                    default:
                        $redirectPath = '../views/login.php';
                        break;
                }

                redirect($redirectPath, 'success', 'Password changed successfully.');
            } else {
                $errors[] = "Password update failed.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        }
    }

}

// Default fallback redirect
else {
    header("Location: /");
    exit();
}

?>
