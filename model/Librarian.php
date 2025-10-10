<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Books.php';
require_once __DIR__ . '/Category.php';

class Librarian extends User {
    private $bookModel;
    private $categoryModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->bookModel = new Books($db);
        $this->categoryModel = new Category($db);
    }

    // Book management
    public function addBook($data) {
        return $this->bookModel->addBook($data);
    }

    public function editBook($bookID, $data) {
        return $this->bookModel->editBook($bookID, $data);
    }

    public function archiveBook($bookID) {
        return $this->bookModel->archiveBook($bookID);
    }

    public function getAllBooks() {
        return $this->bookModel->getAllBooks();
    }

    public function getBooksByCategory($categoryID) {
        return $this->bookModel->getBooksByCategory($categoryID);
    }

    public function searchBooks($keyword, $categoryID = null) {
        return $this->bookModel->searchBooks($keyword, $categoryID);
    }

    public function addCategory($categoryName) {
        return $this->categoryModel->addCategory($categoryName);
    }

    public function editCategory($categoryID, $categoryName) {
        return $this->categoryModel->editCategory($categoryID, $categoryName);
    }

    public function deleteCategory($categoryID) {
        return $this->categoryModel->deleteCategory($categoryID);
    }

    public function getAllCategories() {
        return $this->categoryModel->getAllCategories();
    }
}
?>
