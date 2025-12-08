<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/BorrowModel.php';
require_once __DIR__ . '/AuthModel.php'; 

class StaffModel extends BaseModel {
    private $borrowRequestTable = 'borrow_requests';
    private $borrowTable = 'borrows';
    private $reservationTable = 'reservations';
    private $userTable = 'users';
    private $bookTable = 'books';

    private $borrowModel;
    private $authModel;

    public function __construct() {
        parent::__construct();
        $this->borrowModel = new BorrowModel($this->conn); 
        $this->authModel = new AuthModel($this->conn);   
    }



    // =================== COUNT ==================
    public function countPendingBorrowRequests() {
        $query = "SELECT COUNT(*) AS total FROM {$this->borrowRequestTable} WHERE status='pending'";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function countActiveReservations() {
        $query = "SELECT COUNT(*) AS total FROM {$this->reservationTable} WHERE status='pending'";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function countOverdueLoans() {
        $query = "SELECT COUNT(*) AS total FROM {$this->borrowTable} WHERE due_date < NOW() AND status='borrowed'";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function countTotalCleared() {
        $query = "SELECT COUNT(*) AS total FROM clearance WHERE status='cleared'";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function countStudentWithPenalties() {
        $query = "SELECT COUNT(DISTINCT user_id) AS students_with_penalties
                FROM penalties
                WHERE status = 'unpaid'";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_assoc()['students_with_penalties'] : 0;
    }

    // =================== BORROWED BOOKS ===================
    public function getBorrowedBooks() {
        $query = "SELECT b.user_id,
                         CONCAT(u.firstname, ' ', COALESCE(u.midint, ''), ' ', u.lastname) AS user_name,
                         COUNT(*) AS total_borrowed,
                         SUM(CASE WHEN b.due_date < CURDATE() AND b.status != 'returned' THEN 1 ELSE 0 END) AS overdue,
                         MAX(b.borrow_date) AS last_borrowed
                  FROM {$this->borrowTable} b
                  JOIN {$this->userTable} u ON b.user_id = u.user_id
                  GROUP BY b.user_id, u.firstname, u.midint, u.lastname
                  ORDER BY last_borrowed DESC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getBorrowedSummaryByUser() {
        $query = "SELECT u.user_id,
                         CONCAT_WS(' ', u.firstname, u.midint, u.lastname) AS user_name,
                         u.role,
                         COUNT(borrows.borrow_id) AS total_borrowed,
                         COUNT(CASE WHEN borrows.status = 'returned' THEN 1 END) AS total_returned,
                         SUM(CASE WHEN borrows.due_date < NOW() AND borrows.status = 'borrowed' THEN 1 ELSE 0 END) AS overdue_count,
                         MAX(borrows.borrow_date) AS last_borrowed
                  FROM {$this->borrowTable} borrows
                  JOIN {$this->userTable} u ON borrows.user_id = u.user_id
                  GROUP BY u.user_id
                  ORDER BY last_borrowed DESC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getUserBorrowHistory(int $userId) {
        $query = "SELECT b.title, bo.borrow_date, bo.due_date, bo.return_date, bo.status
                  FROM {$this->borrowTable} bo
                  JOIN {$this->bookTable} b ON bo.book_id = b.book_id
                  WHERE bo.user_id = ?
                  ORDER BY bo.borrow_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    // =================== BORROW REQUEST ===================
    public function getBorrowRequests() {
        $query = "SELECT br.*, 
                         CONCAT_WS(' ', u.firstname, u.midint, u.lastname) AS user_name, 
                         u.role,
                         b.title AS book_title
                  FROM {$this->borrowRequestTable} br
                  JOIN {$this->userTable} u ON br.user_id = u.user_id
                  JOIN {$this->bookTable} b ON br.book_id = b.book_id
                  WHERE br.status = 'pending'
                  ORDER BY br.request_date ASC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function approveBorrowRequest(int $requestId, int $staffId) {
        // Fetch request
        $query = "SELECT * FROM {$this->borrowRequestTable} WHERE request_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$request) return false;

        // Borrow limit check
        $current = $this->borrowModel->getActiveBorrowsByUserId($request['user_id']);
        $limit = $this->borrowModel->getBorrowLimits()[$this->authModel->getUserRole($request['user_id'])] ?? 0;

        if (count($current) >= $limit) {
            return "limit_reached"; // safely indicate user reached borrow limit
        }

        // Approve request
        $update = "UPDATE {$this->borrowRequestTable} 
                   SET status='approved', staff_id=?, decision_date=NOW() 
                   WHERE request_id=?";
        $stmt = $this->conn->prepare($update);
        $stmt->bind_param("ii", $staffId, $requestId);
        $stmt->execute();
        $stmt->close();

        // Finalize borrow
        $borrowDate = date('Y-m-d H:i:s');
        $dueDate = date('Y-m-d H:i:s', strtotime("+14 days"));
        $insert = "INSERT INTO {$this->borrowTable} (user_id, book_id, borrow_date, due_date, status) 
                   VALUES (?, ?, ?, ?, 'borrowed')";
        $stmt = $this->conn->prepare($insert);
        $stmt->bind_param("iiss", $request['user_id'], $request['book_id'], $borrowDate, $dueDate);
        $success = $stmt->execute();
        $stmt->close();

        // Update book availability
        $updateBook = "UPDATE {$this->bookTable} 
                       SET copies_available = copies_available - 1 
                       WHERE book_id = ?";
        $stmt = $this->conn->prepare($updateBook);
        $stmt->bind_param("i", $request['book_id']);
        $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function rejectBorrowRequest(int $requestId, int $staffId) {
        $query = "UPDATE {$this->borrowRequestTable} 
                  SET status='rejected', staff_id=?, decision_date=NOW() 
                  WHERE request_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $staffId, $requestId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ===================== RESERVATION ======================
    public function getReservations() {
        $query = "SELECT r.*, 
                         CONCAT_WS(' ', u.firstname, u.midint, u.lastname) AS user_name,
                         u.role,
                         b.title AS book_title
                  FROM {$this->reservationTable} r
                  JOIN {$this->userTable} u ON r.user_id = u.user_id
                  JOIN {$this->bookTable} b ON r.book_id = b.book_id
                  WHERE r.status = 'pending'
                  ORDER BY r.reservation_date ASC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPendingReservations() {
        return $this->getReservations(); 
    }

    public function approveReservation(int $reservationId, int $staffId) {
        $readyDate = date('Y-m-d H:i:s');
        $expiryDate = date('Y-m-d H:i:s', strtotime("+3 days"));

        $query = "UPDATE {$this->reservationTable} 
                  SET status='ready_for_pickup', ready_date=?, expiry_date=? 
                  WHERE reservation_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $readyDate, $expiryDate, $reservationId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function rejectReservation(int $reservationId) {
        $query = "UPDATE {$this->reservationTable} SET status='rejected' WHERE reservation_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $reservationId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function markReservationPickedUp($reservationId, $staffId) {
        $query = "UPDATE reservations SET status='picked_up' WHERE reservation_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $reservationId);
        return $stmt->execute();
    }

    public function getReservationById($reservationId) {
        $query = "SELECT * FROM reservations WHERE reservation_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getReadyForPickupReservations() {
        $query = "SELECT 
                r.reservation_id, r.user_id, r.book_id, r.reservation_date, r.status, r.ready_date, r.expiry_date,
                TRIM(CONCAT(u.firstname, ' ',COALESCE(u.midint, ''), ' ',u.lastname)) AS full_name,
                u.role, b.title AS book_title
            FROM reservations r
            JOIN users u ON r.user_id = u.user_id
            JOIN books b ON r.book_id = b.book_id
            WHERE r.status = 'ready_for_pickup'
            ORDER BY r.reservation_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // search borrowers
    public function searchBorrowers(string $search = '') {
        $searchQuery = '';
        $params = [];
        $types = '';

        if (!empty($search)) {
            $searchQuery = "WHERE CONCAT_WS(' ', u.firstname, u.midint, u.lastname) LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }

        $query = "SELECT u.user_id,
                        CONCAT_WS(' ', u.firstname, u.midint, u.lastname) AS user_name,
                        u.role
                FROM {$this->userTable} u
                $searchQuery
                ORDER BY u.lastname, u.firstname";

        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $data;
    }

}
?>
