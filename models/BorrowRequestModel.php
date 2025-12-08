<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/BorrowModel.php';

class BorrowRequestModel extends BaseModel {

    private $table = 'borrow_requests';
    private $bookTable = 'books';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    public function __construct() {
        parent::__construct();
    }


    // Create a new borrow request
    public function createRequest(int $userId, int $bookId, string $type = 'borrow') {
        $borrowModel = new BorrowModel();
        $limits = $borrowModel->getBorrowLimits();

        // Get user's role
        $roleQuery = "SELECT role FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($roleQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $role = $stmt->get_result()->fetch_assoc()['role'];
        $stmt->close();

        $maxLimit = $limits[$role] ?? 0;

        // Count active borrow records
        $countQuery = "SELECT COUNT(*) AS active 
                    FROM borrows 
                    WHERE user_id = ? AND status = 'borrowed'";

        $stmt = $this->conn->prepare($countQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $activeBorrows = $stmt->get_result()->fetch_assoc()['active'];
        $stmt->close();

        // LIMIT REACHED
        if ($activeBorrows >= $maxLimit) {
            return [
                "success" => false,
                "message" => "Borrowing limit reached. Return a book before requesting again."
            ];
        }

        // Check pending
        if ($this->hasPendingRequest($userId, $bookId)) {
            return [
                "success" => false,
                "message" => "You already have a pending request for this book."
            ];
        }

        // Insert request
        $query = "INSERT INTO {$this->table} 
                    (user_id, book_id, request_type, request_date, status) 
                VALUES (?, ?, ?, NOW(), ?)";

        $stmt = $this->conn->prepare($query);
        $status = self::STATUS_PENDING;
        $stmt->bind_param("iiss", $userId, $bookId, $type, $status);

        $success = $stmt->execute();
        $stmt->close();

        return [
            "success" => $success,
            "message" => $success ? "Borrow request submitted." : "Failed to submit request."
        ];
    }


    // Check the pending request for a user and book
    public function hasPendingRequest(int $userId, int $bookId): bool
    {
        $query = "SELECT request_id FROM {$this->table} 
                  WHERE user_id = ? AND book_id = ? AND status = ?";
        $stmt = $this->conn->prepare($query);
        $status = self::STATUS_PENDING;
        $stmt->bind_param("iis", $userId, $bookId, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;
        $stmt->close();

        return $count > 0;
    }

    
    // Get all requests by user
    public function getRequestsByUser(int $userId)
    {
        $query = "SELECT br.*, b.title, b.copies_available
                  FROM {$this->table} br
                  JOIN {$this->bookTable} b ON br.book_id = b.book_id
                  WHERE br.user_id = ?
                  ORDER BY br.request_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $requests;
    }

    // Update status of a request
    public function updateStatus(int $requestId, string $status): bool
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_CANCELLED])) {
            return false;
        }

        $query = "UPDATE {$this->table} SET status = ? WHERE request_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $requestId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    // Cancel a pending request by user
    public function cancelRequest(int $userId, int $requestId): bool
    {
        $query = "UPDATE {$this->table} SET status = ? WHERE request_id = ? AND user_id = ? AND status = ?";
        $stmt = $this->conn->prepare($query);
        $status = self::STATUS_CANCELLED;
        $pending = self::STATUS_PENDING;
        $stmt->bind_param("siis", $status, $requestId, $userId, $pending);
        $success = $stmt->execute();
        $stmt->close();

        return $success && $this->conn->affected_rows > 0;
    }
}
