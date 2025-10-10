<?php 
require_once __DIR__ . '/../config/Database.php';

class Borrow {
    private $conn;
    private $table = "borrow";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function borrowBook($userID, $bookID, $due_date, $semester) {
        $this->decrementBookAvailable($bookID);

        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (userID, bookID, due_date, status, semester) VALUES (?, ?, ?, 'pending', ?)"
        );
        $stmt->bind_param("iiss", $userID, $bookID, $due_date, $semester);
        return $stmt->execute();
    }

    public function returnBook($borrowID) {
        // Get bookID to increment availability
        $stmt = $this->conn->prepare("SELECT bookID FROM {$this->table} WHERE borrowID = ?");
        $stmt->bind_param("i", $borrowID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $bookID = $result['bookID'];

        // Increment availability
        $this->incrementBookAvailable($bookID);

        // Update borrow record
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET return_date = NOW(), status = 'returned' WHERE borrowID = ?"
        );
        $stmt->bind_param("i", $borrowID);
        return $stmt->execute();
    }

    // Get all borrows for a user
    public function getBorrowsByUser($userID) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, bk.title, bk.author FROM {$this->table} b
             LEFT JOIN Books bk ON b.bookID = bk.bookID
             WHERE b.userID = ?"
        );
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Helper: decrement book availability
    private function decrementBookAvailable($bookID) {
        $stmt = $this->conn->prepare("UPDATE Books SET available = available - 1 WHERE bookID = ? AND available > 0");
        $stmt->bind_param("i", $bookID);
        return $stmt->execute();
    }

    // Helper: increment book availability
    private function incrementBookAvailable($bookID) {
        $stmt = $this->conn->prepare("UPDATE Books SET available = available + 1 WHERE bookID = ?");
        $stmt->bind_param("i", $bookID);
        return $stmt->execute();
    }
}
?>
