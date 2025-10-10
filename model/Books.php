<?php 
require_once __DIR__ . '/../config/Database.php';

class Books {
    private $conn;
    private $table = "books";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all books
    public function getAllBooks() {
        $query = "SELECT b.*, c.categoryName
                  FROM {$this->table} b
                  LEFT JOIN category c ON b.categoryID = c.categoryID
                  ORDER BY b.date_added DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Add a new book
    public function addBook($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} 
            (title, author, publisher, isbn, categoryID, quantity, available, price, description, status, target_user, date_added, year_published) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW(), ?)"
        );
        $stmt->bind_param(
            "ssssiiidssi",
            $data['title'],
            $data['author'],
            $data['publisher'],
            $data['isbn'],
            $data['categoryID'],
            $data['quantity'],
            $data['available'],
            $data['price'],
            $data['description'],
            $data['target_user'],
            $data['year_published']
        );
        return $stmt->execute();
    }

    // Update book details
    public function editBook($bookID, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
             SET title = ?, author = ?, publisher = ?, isbn = ?, categoryID = ?, 
                 quantity = ?, available = ?, price = ?, description = ?, target_user = ?, year_published = ?
             WHERE bookID = ?"
        );
        $stmt->bind_param(
            "ssssiiidssii",
            $data['title'],
            $data['author'],
            $data['publisher'],
            $data['isbn'],
            $data['categoryID'],
            $data['quantity'],
            $data['available'],
            $data['price'],
            $data['description'],
            $data['target_user'],
            $data['year_published'],
            $bookID
        );
        return $stmt->execute();
    }

    // Archive a single book
    public function archiveBook($bookID) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = 'archived' WHERE bookID = ?");
        $stmt->bind_param("i", $bookID);
        return $stmt->execute();
    }

    // Get books by category
    public function getBooksByCategory($categoryID) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, c.categoryName 
             FROM {$this->table} b
             LEFT JOIN Category c ON b.categoryID = c.categoryID
             WHERE b.categoryID = ? AND b.status = 'active'"
        );
        $stmt->bind_param("i", $categoryID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get book by ID
    public function getBookByID($bookID) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE bookID = ?");
        $stmt->bind_param("i", $bookID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Search books
    public function searchBooks($searchTerm, $categoryID = null) {
        $query = "SELECT * FROM {$this->table} WHERE (title LIKE ? OR author LIKE ?)";
        if ($categoryID) $query .= " AND categoryID = ?";
        $stmt = $this->conn->prepare($query);

        $search = "%" . $searchTerm . "%";
        if ($categoryID) {
            $stmt->bind_param("ssi", $search, $search, $categoryID);
        } else {
            $stmt->bind_param("ss", $search, $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
