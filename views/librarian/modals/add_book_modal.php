<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addBookModalLabel"><i class="fas fa-book"></i> Add New Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBookForm" action="../../controllers/BookController.php" method="POST">
                <input type="hidden" name="action" value="addBook">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="mb-3 text-success">Primary Details</h6>
                            <div class="mb-3">
                                <label for="add_isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="add_isbn" name="isbn" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="add_title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="add_author" name="author" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_publisher" class="form-label">Publisher</label>
                                <input type="text" class="form-control" id="add_publisher" name="publisher">
                            </div>
                            <div class="mb-3">
                                <label for="add_publication_year" class="form-label">Publication Year</label>
                                <input type="number" class="form-control" id="add_publication_year" name="year_published" min="1500" max="<?= date('Y') ?>" value="<?= date('Y') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="add_price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="add_price" name="price" min="0.00" step="0.01" value="0.00" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3 text-success">Library & Category</h6>
                            <div class="mb-3">
                                <label for="add_category_id" class="form-label">Category</label>
                                <select class="form-select" id="add_category_id" name="category_id" required>
                                    <option value="">-- Select Category --</option>
                                    <?php 
                                    // Assumes $all_categories is available from book_management.php
                                    foreach ($all_categories as $cat) {
                                        echo '<option value="' . htmlspecialchars($cat['category_id']) . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="add_copies_total" class="form-label">Total Copies in Inventory</label>
                                <input type="number" class="form-control" id="add_copies_total" name="copies_total" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="target_user" class="form-label">Target User</label>
                                <select class="form-select" id="target_user" name="target_user" required>
                                    <option value="all">All Users (Students & Teachers)</option>
                                    <option value="student">Only Students</option>
                                    <option value="teacher">Only Teachers</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="add_description" class="form-label">Summary / Description</label>
                                <textarea class="form-control" id="add_description" name="description" rows="8"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>