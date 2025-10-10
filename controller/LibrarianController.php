<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Librarian.php';

class LibrarianController {
    private $db;
    private $librarian;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->librarian = new Librarian($this->db);
    }

    // Add Book
    public function addBook() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'author' => $_POST['author'] ?? '',
                'publisher' => $_POST['publisher'] ?? '',
                'isbn' => $_POST['isbn'] ?? '',
                'categoryID' => $_POST['categoryID'] ?? null,
                'quantity' => $_POST['quantity'] ?? 0,
                'available' => $_POST['available'] ?? 0,
                'price' => $_POST['price'] ?? 0.00,
                'description' => $_POST['description'] ?? '',
                'target_user' => $_POST['target_user'] ?? 'all',
                'year_published' => $_POST['year_published'] ?? null
            ];

            $result = $this->librarian->addBook($data);
            $_SESSION[$result ? 'message' : 'error'] = $result ? "Book added successfully!" : "Failed to add book.";
            header("Location: ../views/librarian/books.php");
            exit;
        }
    }

    // Edit Book
    public function editBook() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bookID = $_POST['bookID'];
            $data = [
                'title' => $_POST['title'] ?? '',
                'author' => $_POST['author'] ?? '',
                'publisher' => $_POST['publisher'] ?? '',
                'isbn' => $_POST['isbn'] ?? '',
                'categoryID' => $_POST['categoryID'] ?? null,
                'quantity' => $_POST['quantity'] ?? 0,
                'available' => $_POST['available'] ?? 0,
                'price' => $_POST['price'] ?? 0.00,
                'description' => $_POST['description'] ?? '',
                'target_user' => $_POST['target_user'] ?? 'all',
                'year_published' => $_POST['year_published'] ?? null
            ];

            $result = $this->librarian->editBook($bookID, $data);
            $_SESSION[$result ? 'message' : 'error'] = $result ? "Book updated successfully!" : "Failed to update book.";
            header("Location: ../views/librarian/books.php");
            exit;
        }
    }

    // Archive Book
    public function archiveBook($bookID) {
        $result = $this->librarian->archiveBook($bookID);
        $_SESSION[$result ? 'message' : 'error'] = $result ? "Book archived successfully!" : "Failed to archive book.";
        header("Location: ../views/librarian/books.php");
        exit;
    }

    // View all books
    public function getAllBooks() {
        return $this->librarian->getAllBooks();
    }

    // Filter books by category
    public function getBooksByCategory($categoryID) {
        return $this->librarian->getBooksByCategory($categoryID);
    }

    // Search books
    public function searchBooks() {
        $keyword = $_GET['keyword'] ?? '';
        $categoryID = $_GET['categoryID'] ?? null;
        return $this->librarian->searchBooks($keyword, $categoryID);
    }

    // Category management
    public function addCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoryName = $_POST['categoryName'] ?? '';
            $result = $this->librarian->addCategory($categoryName);
            $_SESSION[$result ? 'message' : 'error'] = $result ? "Category added successfully!" : "Failed to add category.";
            header("Location: ../views/librarian/categories.php");
            exit;
        }
    }

    public function editCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoryID = $_POST['categoryID'];
            $categoryName = $_POST['categoryName'] ?? '';
            $result = $this->librarian->editCategory($categoryID, $categoryName);
            $_SESSION[$result ? 'message' : 'error'] = $result ? "Category updated successfully!" : "Failed to update category.";
            header("Location: ../views/librarian/categories.php");
            exit;
        }
    }

    public function deleteCategory($categoryID) {
        $result = $this->librarian->deleteCategory($categoryID);
        $_SESSION[$result ? 'message' : 'error'] = $result ? "Category deleted successfully!" : "Failed to delete category.";
        header("Location: ../views/librarian/categories.php");
        exit;
    }

    public function getAllCategories() {
        return $this->librarian->getAllCategories();
    }
}
?>
