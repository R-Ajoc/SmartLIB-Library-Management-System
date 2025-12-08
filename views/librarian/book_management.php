<?php
session_start();

define('LIBRARIAN_PAGE', true);

if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'librarian')) {
    header("Location: ../login.php");
    exit();
}


require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/BookModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';

$bookModel = new BookModel();
$categoryModel = new CategoryModel();

$all_books = $bookModel->getAllBooksWithCategoryName();
$all_categories = $categoryModel->getAllCategories();

$success_message = $_SESSION['success_message'] ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['success_message'], $_SESSION['errors']);

$root_path = '../../';
$page_title = "Book Management";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>

    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/jQuery/jquery-ui.css">
    <link href="../../assets/DataTables/datatables.min.css" rel="stylesheet">
    <link href="../../assets/DataTables/responsive.dataTables.min.css" rel="stylesheet">
    <link href="../../assets/librarian.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="dashboard-body">

    <?php require_once 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-book-reader me-2"></i><?= $page_title ?></h2>
                <div>
                    <button class="btn btn-info custom-add-text" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        <i class="fas fa-plus"></i> Add New Book
                    </button>

                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#categoryManagementModal">
                        <i class="fas fa-tags"></i> Category Management
                    </button>
                </div>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <table id="booksTable" class="table table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Total</th>
                            <th>Avail</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_books)): ?>
                            <?php foreach ($all_books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['book_id']) ?></td>
                                    <td><?= htmlspecialchars($book['isbn']) ?></td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= htmlspecialchars($book['category_name']) ?></td>
                                    <td><?= htmlspecialchars($book['copies_total']) ?></td>
                                    <td><?= htmlspecialchars($book['copies_available'] ?? 0) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $book['status'] === 'active' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($book['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <button class="btn btn-sm btn-info edit-book-btn mb-1"
                                                data-book-id="<?= $book['book_id'] ?? 0 ?>"
                                                data-isbn="<?= htmlspecialchars($book['isbn'] ?? '') ?>"
                                                data-title="<?= htmlspecialchars($book['title'] ?? '') ?>"
                                                data-author="<?= htmlspecialchars($book['author'] ?? '') ?>"
                                                data-publisher="<?= htmlspecialchars($book['publisher'] ?? '') ?>"
                                                data-year-published="<?= htmlspecialchars($book['year_published'] ?? '') ?>"
                                                data-category-id="<?= htmlspecialchars($book['category_id'] ?? '') ?>"
                                                data-price="<?= htmlspecialchars(number_format($book['price'] ?? 0.00, 2, '.', '') ) ?>"
                                                data-copies-total="<?= htmlspecialchars($book['copies_total'] ?? 0) ?>"
                                                data-copies-available="<?= htmlspecialchars($book['copies_available'] ?? 0) ?>"
                                                data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                                                data-target-user="<?= htmlspecialchars($book['target_user'] ?? 'all') ?>"
                                                data-status="<?= htmlspecialchars($book['status'] ?? 'active') ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if (($book['status'] ?? 'active') === 'active'): ?>
                                                <button class="btn btn-sm btn-danger status-toggle-btn"
                                                    data-book-id="<?= $book['book_id'] ?? 0 ?>"
                                                    data-current-status="active"
                                                    data-target-status="archive">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-success status-toggle-btn"
                                                    data-book-id="<?= $book['book_id'] ?? 0 ?>"
                                                    data-current-status="archive"
                                                    data-target-status="active">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
                
    </div>

    <div style="height: 50px;"></div>


    <?php require_once 'modals/add_book_modal.php'; ?>
    <?php require_once 'modals/edit_book_modal.php'; ?>
    <?php require_once 'modals/category_manage_modal.php'; ?>

    <script src="../../assets/jQuery/jquery.js"></script>
    <script src="../../assets/jQuery/jquery-ui.js"></script>
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/DataTables/datatables.min.js"></script>
    <script src="../../assets/DataTables/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
                responsive: true,
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: [8] }]
            });

            $('#categoryManagementModal').on('shown.bs.modal', function () {
                if (!$.fn.DataTable.isDataTable('#categoriesTable')) {
                    $('#categoriesTable').DataTable({
                        responsive: true,
                        paging: false,
                        searching: false,
                        info: false,
                        columnDefs: [{ orderable: false, targets: [2] }]
                    });
                }
            });

            $('#editCategoryForm').on('submit', function() {
                const categoryId = $('#edit_category_id').val();
                if (!categoryId) {
                    alert("CRITICAL ERROR: Category ID is missing. Cannot save changes.");
                    return false;
                }
                $(this).find('input[name="category_id"]').val(categoryId);
                return true;
            });

            $(document).on('click', '.edit-category', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#edit_category_id').val(id);
                $('#edit_category_name').val(name);
                $('#categoryManagementModal').modal('hide');
                $('#editCategoryModal').modal('show');
            });

            $(document).on('click', '.delete-category-btn', function() {
                const categoryId = $(this).data('id');
                if (confirm(`Are you sure you want to PERMANENTLY DELETE Category ID ${categoryId}?`)) {
                    const form = $('<form action="../../controllers/CategoryController.php" method="post">' +
                        '<input type="hidden" name="action" value="delete" />' +
                        '<input type="hidden" name="category_id" value="' + categoryId + '" />' +
                        '</form>');
                    $('body').append(form);
                    form.submit();
                }
            });

            $('#editCategoryModal').on('hidden.bs.modal', function () {
                $('#categoryManagementModal').modal('show');
            });

            $(document).on('click', '.status-toggle-btn', function() {
                const bookId = $(this).data('book-id');
                const targetStatus = $(this).data('target-status'); // "archive" or "active"
                const actionText = targetStatus === 'archive' ? 'Archive' : 'Restore';

                if (!bookId) {
                    alert('Invalid Book ID.');
                    return;
                }

                if (confirm(`Are you sure you want to ${actionText} this book (ID: ${bookId})?`)) {
                    $.post('../../controllers/BookController.php', {
                        action: 'setStatus',
                        book_id: bookId,
                        status: targetStatus
                    }, function(response) {
                        console.log(response); // check response in browser console
                        location.reload(); // reload page to refresh counts & status
                    }).fail(function() {
                        alert('Failed to update book status. Check console.');
                    });
                }
            });

            $(document).on('click', '.edit-book-btn', function() {
                const data = $(this).data();

                $('#edit_book_id').val(data.bookId);
                $('#edit_isbn').val(data.isbn);
                $('#edit_title').val(data.title);
                $('#edit_author').val(data.author);
                $('#edit_publisher').val(data.publisher);
                $('#edit_publication_year').val(data.yearPublished);
                $('#edit_price').val(data.price);
                $('#edit_copies_total').val(data.copiesTotal);
                $('#edit_copies_available').val(data.copiesAvailable);
                $('#edit_description').val(data.description);
                $('#edit_category_id').val(data.categoryId);
                //$('#edit_status').val(data.status);
                $('#edit_target_user').val(data.targetUser);

                $('#editBookModal').modal('show');
            });
        });
    </script>

</body>
</html>
