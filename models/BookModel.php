<?php
require_once __DIR__ . '/../config/Database.php';

class BookModel {
    private $conn;
    private $table = 'books';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Add a book
    public function addBook($data) {
    // 10 parameters (status is hardcoded)
    $query = "INSERT INTO " . $this->table . " 
              (isbn, title, author, publisher, year_published, category_id, description, price, copies_total, copies_available, target_user, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ssssiisdiis", 
        $data['isbn'], 
        $data['title'], 
        $data['author'], 
        $data['publisher'], 
        $data['year_published'], 
        $data['category_id'], 
        $data['description'], 
        $data['price'],
        $data['copies_total'], 
        $data['copies_available'],
        $data['target_user'] 
    );

    return $stmt->execute();
}


// Get all books 
public function getAllBooks() {
    $query = "SELECT 
                b.book_id, b.isbn, b.title, b.author, b.publisher, 
                b.year_published, b.category_id, b.description, b.price,
                b.copies_total, b.copies_available, b.target_user, b.status, 
                c.category_name 
              FROM " . $this->table . " b 
              JOIN categories c ON b.category_id = c.category_id 
              ORDER BY b.title ASC";
              
    $result = $this->conn->query($query);
    $books_data = $result->fetch_all(MYSQLI_ASSOC);
    return $books_data; 
}

    // Update book details
    public function updateBook($data) {
    $query = "UPDATE " . $this->table . " SET 
              isbn=?, title=?, author=?, publisher=?, year_published=?, category_id=?, description=?, 
              price=?, copies_total=?, copies_available=?, target_user=?, status=?, updated_at=NOW()
              WHERE book_id=?";

    $stmt = $this->conn->prepare($query);

    
    $stmt->bind_param(
        "ssssiisdiissi", 
        $data['isbn'],             
        $data['title'],            
        $data['author'],           
        $data['publisher'],        
        $data['year_published'],   
        $data['category_id'],      
        $data['description'],      
        $data['price'],            
        $data['copies_total'],    
        $data['copies_available'], 
        $data['target_user'],     
        $data['status'],           
        $data['book_id']           
    );

    return $stmt->execute();
}

    public function getAllBooksWithCategoryName() {
    $query = "SELECT 
                b.book_id, b.isbn, b.title, b.author, b.publisher, b.year_published,
                b.category_id, b.description, b.price, b.copies_total, b.copies_available, 
                b.target_user, b.status,
                c.category_name
            FROM books b
            JOIN categories c ON b.category_id = c.category_id
            ORDER BY b.title ASC";

    $result = $this->conn->query($query);

    if ($result) {
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        return $books;
    }

    return [];
}


    public function getBookById(int $book_id) {
        $query = "SELECT * FROM books WHERE book_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    // Archive or restore actions
    public function setStatus($bookId, $status) {
        $query = "UPDATE " . $this->table . " SET status = ?, updated_at = NOW() WHERE book_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $bookId);
        return $stmt->execute();
    }

    // Count archived books
    public function countArchivedBooks() {
    $query = "SELECT COUNT(*) AS total_archived FROM " . $this->table . " WHERE status = 'archive'";
    $result = $this->conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total_archived'];
    }
    return 0;
}

    // Get total count of all books (active and archived)
    public function getTotalBooksCount() {
        $query = "SELECT COUNT(book_id) as total FROM " . $this->table;
        $result = $this->conn->query($query);
        if ($result) {
        $data = $result->fetch_assoc();
        return (int)$data['total'];
        }
        return 0;
    }

    // Get Total Active Physical Copies count
    public function getTotalCopiesInInventory() {
        $query = "SELECT SUM(copies_total) as total_copies FROM " . $this->table . " WHERE status = 'active'";
        $result = $this->conn->query($query);
        if ($result) {
            $data = $result->fetch_assoc();
            return (int)$data['total_copies'];
        }
        return 0;
    }

    // Get low stock count
    public function getLowStockCount(int $threshold = 5) {
        // Counts how many unique book titles have available copies less than the threshold
        $query = "SELECT COUNT(book_id) as low_stock_count FROM " . $this->table . 
                " WHERE copies_available < " . $threshold . " AND status = 'active'";
        
        $result = $this->conn->query($query);
        
        if ($result) {
            $data = $result->fetch_assoc();
            return (int)$data['low_stock_count'];
        }
        return 0;
    }

    // Get recent activity
    public function getRecentActivity(int $limit = 5) {
        $query = "SELECT title, author, created_at, updated_at 
                FROM " . $this->table . " 
                ORDER BY updated_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $activities = [];
        
        while ($row = $result->fetch_assoc()) {
            $activities[] = $row;
        }
        
        $stmt->close();
        return $activities;
    }

    public function getBooksByUser(string $mode = 'public', int $limit = 20, int $offset = 0) {

        if ($mode === 'teacher_only') {
            // Teacher exclusive books
            $query = "SELECT * FROM books 
                    WHERE target_user = 'teacher'
                    ORDER BY created_at DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $limit, $offset);

        } else {
            // Public books (students and teachers)
            $query = "SELECT * FROM books 
                    WHERE target_user = 'all'
                    ORDER BY created_at DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $result;
    }


}
?>
