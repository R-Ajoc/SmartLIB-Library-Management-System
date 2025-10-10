<?php
require_once __DIR__ . '/../config/Database.php';

class Category {
    private $conn;
    private $table = 'category';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🟢 Add a new category
    public function addCategory($categoryName) {
        $query = "INSERT INTO {$this->table} (categoryName) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $categoryName);
        return $stmt->execute();
    }

    // 🟡 Edit a category name
    public function editCategory($categoryID, $categoryName) {
        $query = "UPDATE {$this->table} SET categoryName = ? WHERE categoryID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $categoryName, $categoryID);
        return $stmt->execute();
    }

    // 🔴 Delete a category
    public function deleteCategory($categoryID) {
        $query = "DELETE FROM {$this->table} WHERE categoryID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryID);
        return $stmt->execute();
    }

    // 📚 Get all categories
    public function getAllCategories() {
        $query = "SELECT * FROM {$this->table} ORDER BY categoryName ASC";
        $result = $this->conn->query($query);
        return $result;
    }

    // 🔍 Get a single category by ID
    public function getCategoryByID($categoryID) {
        $query = "SELECT * FROM {$this->table} WHERE categoryID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🔍 Search categories by name (optional)
    public function searchCategory($keyword) {
        $query = "SELECT * FROM {$this->table} WHERE categoryName LIKE ?";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        return $stmt->get_result();
    }
}


