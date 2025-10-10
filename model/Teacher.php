<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Borrow.php';
require_once __DIR__ . '/Books.php';

class Teacher {
    private $conn;
    private $borrowModel;
    private $bookModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->borrowModel = new Borrow($db);
        $this->bookModel = new Books($db);
    }

    // Borrow a book (no limit for teachers)
    public function borrowBook($userID, $bookID, $due_date, $semester) {
        // Check if the book is available
        $book = $this->bookModel->getBookByID($bookID);
        if (!$book || $book['available'] <= 0) {
            return [
                'success' => false,
                'message' => 'This book is currently unavailable.'
            ];
        }

        // Proceed to borrow
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

    // Check clearance requirement at semester end
    public function checkClearanceStatus($userID, $semester) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS unreturned_books 
             FROM Borrow 
             WHERE userID = ? 
               AND semester = ? 
               AND status IN ('pending', 'approved', 'overdue')"
        );
        $stmt->bind_param("is", $userID, $semester);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['unreturned_books'] > 0) {
            return [
                'cleared' => false,
                'message' => "You must return all borrowed books to be cleared for this semester."
            ];
        }

        return ['cleared' => true, 'message' => "You are cleared for this semester."];
    }
}
?>
