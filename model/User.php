<?php 
// this class extends Person.php and will handle all user-related database logic.

require_once __DIR__ . '/../config/Database.php';
require_once 'Person.php';

class User extends Person {
	private $conn;
	private $table = "users";

	public function __construct($db) {
		$this->conn = $db;
	}

	// SIGN UP (For Student/Teacher)
	public function signUp($userID, $email, $username, $password) {
		// Check if the user exists
		$query = "SELECT userID, email, username, password FROM {$this->table} WHERE userID = ? OR email = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("ss", $userID, $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows === 0) {
			return ['status' => false, 'message' => 'User does not exist in the system.'];
		}

		$user = $result->fetch_assoc();

		// prevent duplicate activation
		if (!empty($user['username']) && !empty($user['password'])) {
			return ['status' => false, 'message' => 'Account already activated. Please log in.'];
		}

		// check if username is taken
		$checkUsername = "SELECT username FROM {$this->table} WHERE username = ?";
		$stmtCheck = $this->conn->prepare($checkUsername);
		$stmtCheck->bind_param("s", $username);
		$stmtCheck->execute();
		$resultCheck = $stmtCheck->get_result();

		if ($resultCheck->num_rows > 0) {
			return ['status' => false, 'message' => 'Username is already taken. Please choose another one.'];
		}

		// Hash and update the password
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		$update = "UPDATE {$this->table} 
		           SET username = ?, password = ?, status = 'active'
		           WHERE userID = ? AND email = ?";
		$stmt2 = $this->conn->prepare($update);
		$stmt2->bind_param("ssss", $username, $hashedPassword, $userID, $email);

		if ($stmt2->execute() && $stmt2->affected_rows > 0) {
			return ['status' => true, 'message' => 'Registration successful! You can now log in.'];
		}

		if ($user['role'] == 'librarian' || $user['role'] == 'staff') {
    		return ['status' => false, 'message' => 'Staff and librarians cannot self-register.'];
		}

		return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
	}


	// LOGIN (For All Users)
	public function login($username, $password) {
		$query = "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows === 0) {
			return ['status' => false, 'message' => 'Username not found.'];
		}

		$user = $result->fetch_assoc();

		if (empty($user['password'])) {
			return ['status' => false, 'message' => 'Account not yet activated.'];
		}

		if (password_verify($password, $user['password'])) {
			session_start();
			$_SESSION['userID'] = $user['userID'];
			$_SESSION['role'] = $user['role'];
			$_SESSION['firstname'] = $user['firstname'];

			// cookies
			if (!empty($_POST['remember'])) {
				setcookie("username", $user['username'], time() + (86400 * 7), "/"); // 30 days
				setcookie("role", $user['role'], time() + (86400 * 7), "/");
			}

			return ['status' => true, 'message' => 'Login successful.', 'data' => $user];
		}

		return ['status' => false, 'message' => 'Invalid password.'];
	}

	// LOGOUT
	public function logout() {
		session_start();
		session_unset();
		session_destroy();

		// Remove cookies
		if (isset($_COOKIE['username'])) {
			setcookie('userID', '', time() - 3600, '/');
		}
		if (isset($_COOKIE['role'])) {
			setcookie('role', '', time() - 3600, '/');
		}

		header("Location: login.php");
		exit();
	}

	public function getUserByID($userID) {
		$query = "SELECT * FROM users WHERE userID = :userID LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam('i', $userID);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			return ['status' => true, 'data' => $result->fetch_assoc()];
		} else {
			return ['status' => false, 'message' => 'User not found'];
		}
	}
}

?>
