<?php

require_once __DIR__ . '/BaseModel.php';

class ReservationModel extends BaseModel {
    private $table = 'reservations';
    private $bookTable = 'books';
    const PICKUP_WINDOW_DAYS = 3;

    public function __construct() {
        parent::__construct();
    }

   
    // Finalize a reservation by setting it to ready for pickup
    public function finalizeReservation($reservationId) {
        // Set ready and expiry date
        $readyDate = date('Y-m-d H:i:s');
        $expiryDate = date('Y-m-d H:i:s', strtotime("+".self::PICKUP_WINDOW_DAYS." days"));

        $query = "UPDATE {$this->table} 
                  SET status = 'ready_for_pickup', ready_date = ?, expiry_date = ? 
                  WHERE reservation_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $readyDate, $expiryDate, $reservationId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    // check whether user has an active reservation for a specific book
    public function hasActiveReservation($userId, $bookId) {
        $query = "SELECT reservation_id FROM {$this->table} 
                  WHERE user_id = ? AND book_id = ? AND status IN ('pending','ready_for_pickup')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $userId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;
        $stmt->close();

        return $count > 0;
    }


    // Get total active reservations for a user
    public function getTotalActiveReservations($userId) {
        $query = "SELECT COUNT(reservation_id) AS total_reservations 
                  FROM {$this->table} 
                  WHERE user_id = ? AND status IN ('pending', 'ready_for_pickup')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        return (int)($data['total_reservations'] ?? 0);
    }


    // Fetch reservations for a specific user
    public function getReservationsByUser(int $userId) {
        $query = "SELECT r.*, b.title, b.author 
                  FROM {$this->table} r
                  JOIN {$this->bookTable} b ON r.book_id = b.book_id
                  WHERE r.user_id = ?
                  ORDER BY r.reservation_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $reservations;
    }

   
    // Check if user has a pending reservation for a specific book
    public function hasPendingReservation(int $userId, int $bookId): bool {
        $query = "SELECT reservation_id 
                  FROM {$this->table} 
                  WHERE user_id = ? AND book_id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $userId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

   
    // Create a new reservation
    public function createReservation(int $userId, int $bookId): ?int {
        $now = date('Y-m-d H:i:s');
        $status = 'pending';
        $query = "INSERT INTO {$this->table} (user_id, book_id, reservation_date, status)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiss", $userId, $bookId, $now, $status);
        $success = $stmt->execute();
        $id = $success ? $stmt->insert_id : null;
        $stmt->close();
        return $id;
    }

}

