<?php

require_once __DIR__ . '/BaseModel.php';

class BorrowModel extends BaseModel {
    private $table = 'borrows';
    private $bookTable = 'books';

    public function __construct($conn = null) {
        parent::__construct($conn);
    }


    // Create a new borrow record
    public function createBorrow($userId, $bookId) {
        $borrowDate = date("Y-m-d H:i:s");
        $dueDate = date("Y-m-d H:i:s", strtotime("+7 days"));

        $query = "INSERT INTO borrows (user_id, book_id, borrow_date, due_date, status)
                VALUES (?, ?, ?, ?, 'borrowed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiss", $userId, $bookId, $borrowDate, $dueDate);
        return $stmt->execute();
    }


   
    // Finalize borrow process: decrease inventory and record borrow
    public function finalizeBorrow($userId, $bookId, $dueDate) {
        $this->conn->begin_transaction();

        try {
            if (!$this->decrementBookInventory($bookId)) {
                throw new Exception("Book unavailable or inventory update failed.");
            }

            $query = "INSERT INTO {$this->table} 
                      (user_id, book_id, borrow_date, due_date, status)
                      VALUES (?, ?, NOW(), ?, 'borrowed')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iis", $userId, $bookId, $dueDate);
            if (!$stmt->execute()) {
                throw new Exception("Failed to record borrow.");
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Borrow finalize failed: " . $e->getMessage());
            return false;
        }
    }

    // Decrease available copies when a book is borrowed
    private function decrementBookInventory($bookId) {
        $query = "UPDATE {$this->bookTable} 
                  SET copies_available = copies_available - 1 
                  WHERE book_id = ? AND copies_available > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $rowsAffected = $stmt->affected_rows;
        $stmt->close();

        return $rowsAffected > 0;
    }

    // Check if a book is available for borrowing
    public function isBookAvailable($bookId) {
        $query = "SELECT copies_available FROM {$this->bookTable} 
                  WHERE book_id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        $stmt->close();

        return ($book['copies_available'] ?? 0) > 0;
    }

    // Get active borrows for a user
    public function getActiveBorrowsByUserId(int $userId) {
        $query = "SELECT br.borrow_id, br.book_id, b.title, b.price, br.borrow_date, br.due_date, br.status
                FROM borrows br
                JOIN books b ON br.book_id = b.book_id
                WHERE br.user_id = ? AND br.status = 'borrowed'
                ORDER BY br.borrow_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Get total outstanding fines for a user
    public function getTotalOutstandingFines(int $userId): float {
        $query = "SELECT SUM(fine_amount) AS total_fines 
                  FROM {$this->table} 
                  WHERE user_id = ? AND status = 'borrowed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return floatval($result['total_fines'] ?? 0);
    }

    // Get borrow limits based on role
    public function getBorrowLimits(?string $role = null) {
        $limits = [
            'student' => 3,
            'teacher' => 99
        ];

        if ($role !== null) {
            return $limits[$role] ?? 0;
        }

        return $limits;
    }

    // Count overdue books
    public function countOverdueBooks() {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE due_date < CURDATE() AND return_date IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['total'];
    }

    // Get all active borrows with user and book details
    public function getAllActiveBorrows() {
        $query = "SELECT br.borrow_id, br.user_id, br.book_id,            
                        CONCAT(u.firstname, ' ', u.lastname) AS full_name,
                        u.role,
                        b.title AS book_title,
                        b.price,
                        br.borrow_date,
                        br.due_date,
                        br.status
                FROM {$this->table} br
                JOIN users u ON br.user_id = u.user_id
                JOIN {$this->bookTable} b ON br.book_id = b.book_id
                WHERE br.status = 'borrowed' AND u.role = 'student'
                ORDER BY br.due_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    // Mark a borrow as returned
    public function markReturned(int $borrowId) {
        $query = "UPDATE {$this->table} 
                SET status = 'returned', return_date = NOW() 
                WHERE borrow_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $borrowId);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success; 
    }


}
