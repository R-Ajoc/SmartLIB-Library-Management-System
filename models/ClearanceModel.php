<?php
require_once __DIR__ . '/BaseModel.php';

class ClearanceModel extends BaseModel {

    private $clearanceTable = 'clearance';
    private $borrowsTable = 'borrows';
    private $booksTable = 'books';

    public function __construct() {
        parent::__construct();
    }

    // Fetch users for clearance with all necessary info
    public function getUsersForClearance(?string $roleFilter = null) {
    // Role condition
        $roleCondition = '';
        if ($roleFilter) {
            $roleCondition = "WHERE u.role = '{$this->conn->real_escape_string($roleFilter)}'";
        }

        $query = "SELECT u.user_id, CONCAT_WS(' ', u.firstname, u.midint, u.lastname) AS full_name,
                        c.status AS clearance_status,
                        c.cleared_at,
                        -- Count active borrows
                        (SELECT COUNT(*) FROM borrows 
                        WHERE user_id = u.user_id AND status = 'borrowed') AS active_borrows,
                        -- Count overdue books
                        (SELECT COUNT(*) FROM borrows 
                        WHERE user_id = u.user_id AND status = 'borrowed' AND due_date < NOW()) AS overdue_books,
                        -- Total penalty for overdue books
                        (SELECT IFNULL(SUM(b.price),0)
                        FROM borrows br
                        JOIN books b ON br.book_id = b.book_id
                        WHERE br.user_id = u.user_id AND br.status != 'returned' AND br.due_date < NOW()) AS total_penalty
                FROM users u
                LEFT JOIN clearance c ON u.user_id = c.user_id
                {$roleCondition}
                ORDER BY u.lastname, u.firstname";

        $result = $this->conn->query($query);
        $users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        // Force pending if user has active borrows
        foreach ($users as &$user) {
            if ($user['active_borrows'] > 0) {
                $user['clearance_status'] = 'pending';
            } elseif (empty($user['clearance_status'])) {
                $user['clearance_status'] = 'pending';
            }
        }

        return $users;
    }

    // Fetch unreturned book details for a user
    public function getUnreturnedBooks($userId) {
        $sql = "SELECT b.title, br.status, br.due_date, b.price
                FROM {$this->borrowsTable} br
                JOIN {$this->booksTable} b ON br.book_id = b.book_id
                WHERE br.user_id = ? AND br.status != 'returned'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Mark user as cleared
    public function markUserCleared($userId, $staffId) {
        $sql = "UPDATE {$this->clearanceTable}
                SET status = 'cleared',
                    cleared_by = ?,
                    cleared_at = NOW()
                WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $staffId, $userId);
        return $stmt->execute();
    }

    public function markUserPending($userId) {
        $sql = "UPDATE {$this->clearanceTable} SET status = 'pending', cleared_at = NULL WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Reset clearance to pending if user has active borrows
    public function resetClearanceIfBorrowed($userId) {
        $sql = "UPDATE clearance 
                SET status = 'pending', cleared_at = NULL
                WHERE user_id = ? AND EXISTS (
                    SELECT 1 FROM borrows WHERE user_id = ? AND status = 'borrowed'
                )";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $userId);
        return $stmt->execute();
    }


}
