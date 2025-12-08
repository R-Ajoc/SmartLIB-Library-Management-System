<?php
require_once __DIR__ . '/BaseModel.php';

class PenaltyModel extends BaseModel {
    private $table = 'penalties';

    public function __construct() {
        parent::__construct();
    }

    // Create penalty
    public function createPenalty($borrowId, $userId, $amount, $description) {
        $query = "INSERT INTO {$this->table} (borrow_id, user_id, amount, description, status, created_at)
                  VALUES (?, ?, ?, ?, 'unpaid', NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iids", $borrowId, $userId, $amount, $description);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Check if penalty exists for a borrow
    public function getPenaltyByBorrowId($borrowId) {
        $query = "SELECT * FROM {$this->table} WHERE borrow_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $borrowId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ?: false;
    }

    // Mark penalty as paid
    public function markPenaltyPaid($penaltyId) {
        $query = "UPDATE {$this->table} SET status='paid', paid_at=NOW() WHERE penalty_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $penaltyId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Get all unpaid penalties for a user
    public function getUnpaidPenaltiesByUser($userId) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? AND status = 'unpaid'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Summary for the table
    public function getAllPenaltiesSummary() {
        $query = "SELECT 
                    u.user_id, 
                    CONCAT(u.firstname, ' ', u.lastname) AS full_name,
                    SUM(p.amount) AS total_amount,
                    MAX(p.created_at) AS last_updated,
                    SUM(CASE WHEN p.status = 'unpaid' THEN 1 ELSE 0 END) AS unpaid_count
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.user_id
                GROUP BY u.user_id
                ORDER BY last_updated DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function markAllPenaltiesPaidByUser(int $userId) {
        $query = "UPDATE penalties SET status = 'paid', paid_at = NOW() WHERE user_id = ? AND status = 'unpaid'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        return true;
    }

}
