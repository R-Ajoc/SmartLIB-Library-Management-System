<?php

session_start();


require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/CategoryModel.php';


if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'librarian' && $_SESSION['role'] !== 'staff')) {
    header("Location: ../login.php");
    exit();
}

$categoryModel = new CategoryModel();
$errors = [];
$success = false;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $name = trim($_POST['category_name'] ?? '');
            
            if (empty($name)) {
                $errors[] = "Category name is required.";
            } else {
                if ($categoryModel->addCategory($name)) {
                    $_SESSION['success_message'] = "Category '{$name}' added successfully.";
                    $success = true;
                } else {
                    $errors[] = "Failed to add category. Database error or duplicate entry.";
                }
            }
            break;

        case 'update':
            $id = $_POST['category_id'] ?? null;
            $name = trim($_POST['category_name'] ?? '');

            if (empty($id) || empty($name)) {
                $errors[] = "Category ID and name are required for update.";
            } else {
                if ($categoryModel->updateCategory($id, $name)) {
                    $_SESSION['success_message'] = "Category '{$name}' updated successfully.";
                    $success = true;
                } else {
                    $errors[] = "Failed to update category. Database error.";
                }
            }
            break;

        case 'delete':
            $id = $_POST['category_id'] ?? null;
            
            if (empty($id)) {
                $errors[] = "Category ID is missing for deletion.";
            } else {
                if ($categoryModel->deleteCategory($id)) {
                    $_SESSION['success_message'] = "Category successfully deleted.";
                    $success = true;
                } else {
                    $errors[] = "Failed to delete category. Database error.";
                }
            }
            break;

        default:
            $errors[] = "Invalid action specified.";
            break;
    }
} else {
    $errors[] = "Invalid request method or missing action.";
}

// FLASH MESSAGES + REDIRECT
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
}

header("Location: ../views/librarian/book_management.php");
exit();