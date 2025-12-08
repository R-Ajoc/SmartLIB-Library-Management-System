<?php
require_once __DIR__ . '/../config/Database.php'; 

class BaseModel {
    protected $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
}