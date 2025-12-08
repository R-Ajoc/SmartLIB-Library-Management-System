<?php 
require_once __DIR__ . '/../config/Database.php';


class AuthModel {
    private $db;
    private $conn;
    private $table = 'users';

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }


    // Register user
    public function registerUser($data) {

        $query = "INSERT INTO " . $this->table . "
                  (firstname, midint, lastname, email, username, password, role, status, created_at, updated_at)
                  VALUES (?, ?, ?, ? , ?, ?, ?, 'active', NOW(), NOW())";
        
        
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            error_log("AuthModel: Statement preparation field: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "sssssss",
            $data['firstname'],
            $data['midint'],
            $data['lastname'],
            $data['email'],
            $data['username'],
            $data['password'],
            $data['role']
        );

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("AuthModel: User registration failed: " . $stmt->error);
            return false;
        }
    }


    // Check if user exists
    public function userExists($username, $email) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE username = ? OR email = ?  LIMIT 1";
            
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log("AuthModel: Statement preparation failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }


    // Get user by username 
    public function getUserByUsername($username) {
      $query = "SELECT user_id, username, password, role, firstname, email, remember_token FROM " . $this->table . " WHERE username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
             error_log("AuthModel: Get user by username check failed: " . $this->conn->error);
             return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }  


    public function getUserColumn(int $userId, string $column) {
        $allowedColumns = ['role', 'email', 'name', 'firstname', 'lastname']; 
        if (!in_array($column, $allowedColumns)) {
            return null; 
        }

        $query = "SELECT {$column} FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result[$column] ?? null;
    }

    // shortcut to use the function
    public function getUserRole(int $userId) {
        return $this->getUserColumn($userId, 'role') ?? 'student';
    }

    // Get user by ID
    public function getUserById(int $userId) {
        $query = "SELECT user_id, firstname, midint, lastname, email, username, role, created_at 
                FROM users 
                WHERE user_id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result;
    }

    // Update user profile
    public function updateUserProfile(int $userId, array $data) {
        $query = "UPDATE users 
                SET firstname = ?, midint = ?, lastname = ?, email = ?, username = ?, updated_at = NOW() 
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("AuthModel: updateUserProfile prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "sssssi",
            $data['firstname'],
            $data['midint'],
            $data['lastname'],
            $data['email'],
            $data['username'],
            $userId
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("AuthModel: updateUserProfile execute failed: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }

    // Update user password
    public function updateUserPassword(int $userId, string $hashedPassword) {
        $query = "UPDATE users 
                SET password = ?, updated_at = NOW() 
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("AuthModel: updateUserPassword prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("si", $hashedPassword, $userId);

        $result = $stmt->execute();
        if (!$result) {
            error_log("AuthModel: updateUserPassword execute failed: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }


    // Update remember me token
    public function updateRememberToken($userId, $token) {
        $query = "UPDATE " . $this->table . " SET remember_token = ? WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log("AuthModel: Update remember token failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("si", $token, $userId);
        return $stmt->execute();
    }


    // Get user by remember me token
    public function getUserByRememberToken($token) {
        $query = "SELECT user_id, username, role, firstname, email 
                FROM users 
                WHERE remember_token = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return null;

        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->num_rows === 1 ? $result->fetch_assoc() : null;
    }

    
    // Check if user is cleared for borrowing
    public function isUserClearedForBorrowing($userId, $role) {
        if ($role === 'librarian' || $role === 'staff') {
            return true;
        }

        $outstandingCount = 0;

        $query = "SELECT COUNT(borrow_id) FROM borrows WHERE user_id = ? AND status IN ('borrowed','overdue')";
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($outstandingCount);
            $stmt->fetch();
            $stmt->close();
        } else {
            $outstandingCount = 999;
            error_log("MySQLi Clearance Check failed: " . $this->conn->error);
        }
            return $outstandingCount === 0;
    }

    
}
?>