<?php
session_start();

require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../config/Database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'librarian' && $_SESSION['role'] !== 'staff')) {
        header("Location: ../views/login.php");
        exit();
    }

    $action = $_POST['action'];
    $bookModel = new BookModel();
    $categoryModel = new CategoryModel();
    $errors = [];
    $success = '';

    // BOOK MANAGEMENT 

    if ($action === 'addBook') {
        $data = [
            'isbn'             => sanitizeInput($_POST['isbn'] ?? ''),
            'title'            => sanitizeInput($_POST['title'] ?? ''),
            'author'           => sanitizeInput($_POST['author'] ?? ''),
            'publisher'        => !empty($_POST['publisher']) ? sanitizeInput($_POST['publisher']) : null,
            'year_published'   => !empty($_POST['year_published']) ? (int)sanitizeInput($_POST['year_published']) : null,
            'category_id'      => (int)sanitizeInput($_POST['category_id'] ?? 0),
            'description'      => !empty($_POST['description']) ? sanitizeInput($_POST['description']) : null,
            'price'            => (float)sanitizeInput($_POST['price'] ?? 0.00),
            'copies_total'     => (int)sanitizeInput($_POST['copies_total'] ?? 0),
            'copies_available' => (int)sanitizeInput($_POST['copies_total'] ?? 0),
            'target_user'      => sanitizeInput($_POST['target_user'] ?? 'all'),
        ];

        if (empty($data['title']) || empty($data['author']) || $data['category_id'] === 0 || $data['copies_total'] === 0) {
            $errors[] = "Title, Author, Category, and Total Copies are required fields.";
        }

        if (empty($errors)) {
            if ($bookModel->addBook($data)) {
                $success = "Book '{$data['title']}' added successfully.";
            } else {
                $errors[] = "Failed to add book due to a database error.";
            }
        }
    }

    if ($action === 'updateBook') {
        $data = [
            'book_id'          => (int)sanitizeInput($_POST['book_id'] ?? 0),
            'isbn'             => sanitizeInput($_POST['isbn'] ?? ''),
            'title'            => sanitizeInput($_POST['title'] ?? ''),
            'author'           => sanitizeInput($_POST['author'] ?? ''),
            'publisher'        => !empty($_POST['publisher']) ? sanitizeInput($_POST['publisher']) : null,
            'year_published'   => !empty($_POST['year_published']) ? (int)sanitizeInput($_POST['year_published']) : null,
            'category_id'      => (int)sanitizeInput($_POST['category_id'] ?? 0),
            'description'      => !empty($_POST['description']) ? sanitizeInput($_POST['description']) : null,
            'price'            => (float)sanitizeInput($_POST['price'] ?? 0.00),
            'copies_total'     => (int)sanitizeInput($_POST['copies_total'] ?? 0),
            'copies_available' => (int)sanitizeInput($_POST['copies_available'] ?? 0),
            'target_user'      => sanitizeInput($_POST['target_user'] ?? 'all'),
            'status'           => sanitizeInput($_POST['status'] ?? 'active')
        ];

        if ($data['book_id'] === 0) {
            $errors[] = "Invalid Book ID for update.";
        }

        if (empty($errors) && $bookModel->updateBook($data)) {
            $success = "Book details updated successfully.";
        } else {
            $errors[] = "Failed to update book.";
        }
    }

    if ($action === 'setStatus') {
        $bookId = (int)sanitizeInput($_POST['book_id'] ?? 0);
        $status = sanitizeInput($_POST['status'] ?? 'active');

        if ($bookId > 0 && $bookModel->setStatus($bookId, $status)) {
            $success = "Book status updated to '{$status}'.";
        } else {
            $errors[] = "Failed to update book status.";
        }
    }

    
    //   CATEGORY MANAGEMENT 

    if ($action === 'addCategory') {
        $categoryName = sanitizeInput($_POST['category_name'] ?? '');
        if (empty($categoryName)) {
            $errors[] = "Category name cannot be empty.";
        } elseif ($categoryModel->addCategory($categoryName)) {
            $success = "Category '{$categoryName}' added successfully.";
        } else {
            $errors[] = "Failed to add category. It may already exist.";
        }
    }

    if ($action === 'updateCategory') {
        $categoryId = (int)sanitizeInput($_POST['category_id'] ?? 0);
        $categoryName = sanitizeInput($_POST['category_name'] ?? '');

        if ($categoryId === 0 || empty($categoryName)) {
            $errors[] = "Invalid category ID or name.";
        } elseif ($categoryModel->updateCategory($categoryId, $categoryName)) {
            $success = "Category updated successfully.";
        } else {
            $errors[] = "Failed to update category.";
        }
    }

    if ($action === 'deleteCategory') {
        $categoryId = (int)sanitizeInput($_POST['category_id'] ?? 0);

        if ($categoryId === 0) {
            $errors[] = "Invalid category ID.";
        } elseif ($categoryModel->deleteCategory($categoryId)) {
            $success = "Category deleted successfully.";
        } else {
            $errors[] = "Cannot delete category. Books are still assigned to it.";
        }
    }

    // FLASH MESSAGES + REDIRECT

    if (!empty($success)) {
        $_SESSION['success_message'] = $success;
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    header("Location: ../views/librarian/book_management.php");
    exit();
}

// FOR EXCLUSIVE BOOK 

if (isset($_GET['action']) && $_GET['action'] === 'viewBooks') {

    if (!isset($_SESSION['is_logged_in'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $role = $_SESSION['role'];  // student OR teacher
    $bookModel = new BookModel();

    // NEW MODEL FUNCTION YOU WILL ADD
    $books = $bookModel->getBooksByUser($role);

    $root_path = "../";

    if ($role === 'teacher') {
        include __DIR__ . '/../views/teacher/teacher_books.php';
    } else {
        include __DIR__ . '/../views/student/student_catalog.php';
    }

    exit();
}

// Default redirect if file accessed without action
header("Location: ../views/librarian/dashboard.php");
exit();
