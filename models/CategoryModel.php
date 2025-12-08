<?php 
require_once __DIR__ . '/../config/Database.php';

class CategoryModel {
    private $conn;
    private $table = 'categories';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection(); 
    }

    public function addCategory($name) {
        $query = "INSERT INTO " . $this->table . " (category_name) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $name); 
        return $stmt->execute();
    }

    
    public function getAllCategories() {
        $query = "SELECT category_id, category_name 
                  FROM " . $this->table . " 
                  ORDER BY category_name ASC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    
    public function updateCategory($id, $name) {
        $query = "UPDATE " . $this->table . " 
                  SET category_name = ?, updated_at = NOW() 
                  WHERE category_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $name, $id); 
        return $stmt->execute();
    }

  
    public function deleteCategory($category_id) {
        $query = "DELETE FROM " . $this->table . " WHERE category_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        return $stmt->execute();
    }
    
}