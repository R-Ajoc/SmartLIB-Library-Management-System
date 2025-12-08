<?php
require_once __DIR__ . '/../config/Database.php'; 

class CatalogModel {
    private $conn;
    private $table = 'books'; 

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getCatalogBooks($searchTerm = '', $categoryId = 0, $limit = 8, $offset = 0, $allowedTargets = ['all']) {

        // Build placeholders for allowed target_user list
        $placeholders = implode(',', array_fill(0, count($allowedTargets), '?'));

        $query = "SELECT 
                    b.book_id, b.title, b.year_published, b.description, b.copies_available,
                    b.author, b.publisher, b.target_user,
                    c.category_name 
                FROM {$this->table} b
                JOIN categories c ON b.category_id = c.category_id
                WHERE b.status = 'active'
                AND b.target_user IN ($placeholders)";
        
        $params = $allowedTargets;
        $types = str_repeat('s', count($allowedTargets));

        // Search filter
        if (!empty($searchTerm)) {
            $query .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
            $like = "%{$searchTerm}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $types .= 'sss';
        }

        // Category filter
        if ($categoryId > 0) {
            $query .= " AND b.category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }

        // Pagination
        $query .= " ORDER BY b.title ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }


    public function getAllCategories() {
        $query = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalCatalogBooksCount($searchTerm = '', $categoryId = 0, $allowedTargets = ['all']) {

        $placeholders = implode(',', array_fill(0, count($allowedTargets), '?'));

        $query = "SELECT COUNT(b.book_id) AS total_count
                FROM {$this->table} b
                JOIN categories c ON b.category_id = c.category_id
                WHERE b.status = 'active'
                AND b.target_user IN ($placeholders)";
        
        $params = $allowedTargets;
        $types = str_repeat('s', count($allowedTargets));

        if (!empty($searchTerm)) {
            $query .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
            $like = "%{$searchTerm}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $types .= 'sss';
        }

        if ($categoryId > 0) {
            $query .= " AND b.category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)$data['total_count'];
    }


    public function getLatestBooks(int $limit = 6): array {
        $query = "SELECT * FROM {$this->table} WHERE status='active' ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }

}