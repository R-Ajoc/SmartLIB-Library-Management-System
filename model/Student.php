<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Borrow.php';
require_once __DIR__ . '/Books.php';

class Student {
    private $conn;
    private $borrowModel;
    private $bookModel;
    private $borrowLimit = 3; // per semester

    public function __construct($db) {
        $this->conn = $db;
        $this->borrowModel = new Borrow($db);
        $this->bookModel = new Books($db);
    }

    // Borrow a book (with limit check)
    public function borrowBook($userID, $bookID, $due_date, $semester) {
        // Check if the student already reached borrow limit
        if ($this->hasReachedBorrowLimit($userID, $semester)) {
            return [
                'success' => false,
                'message' => 'You have reached your maximum borrow limit of 3 books this semester.'
            ];
        }

        // Check if book is available
        $book = $this->bookModel->getBookByID($bookID);
        if (!$book || $book['available'] <= 0) {
            return [
                'success' => false,
                'message' => 'This book is currently unavailable.'
            ];
        }

        // Create borrow record
        $result = $this->borrowModel->borrowBook($userID, $bookID, $due_date, $semester);

        if ($result) {
            return ['success' => true, 'message' => 'Book borrowed successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to borrow book.'];
        }
    }

    // Return a borrowed book
    public function returnBook($borrowID) {
        $result = $this->borrowModel->returnBook($borrowID);
        if ($result) {
            return ['success' => true, 'message' => 'Book returned successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to return book.'];
        }
    }

    // View all borrowed books
    public function viewBorrowedBooks($userID) {
        return $this->borrowModel->getBorrowsByUser($userID);
    }

    // Check if student reached borrow limit
    private function hasReachedBorrowLimit($userID, $semester) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS active_borrows
             FROM Borrow
             WHERE userID = ? 
               AND semester = ? 
               AND status IN ('pending', 'approved', 'overdue')"
        );
        $stmt->bind_param("is", $userID, $semester);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['active_borrows'] >= $this->borrowLimit;
    }
}
?>

