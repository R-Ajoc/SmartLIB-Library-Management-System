<div class="modal fade" id="categoryManagementModal" tabindex="-1" aria-labelledby="categoryManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="categoryManagementModalLabel"><i class="fas fa-tags"></i> Manage Book Categories</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-5 border-end"> <h6 class="mb-3">Add New Category</h6>
                        <form id="addCategoryForm" action="../../controllers/CategoryController.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                                <div class="form-text">e.g., Fiction, Science, History.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Category
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-7"> <h6 class="mb-3">Existing Categories</h6>
                        <table id="categoriesTable" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($all_categories)): ?>
                                    <?php foreach ($all_categories as $category): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($category['category_id']) ?></td>
                                            <td><?= htmlspecialchars($category['category_name']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info edit-category" 
                                                        data-id="<?= htmlspecialchars($category['category_id']) ?>" 
                                                        data-name="<?= htmlspecialchars($category['category_name']) ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-category-btn"
                                                        data-id="<?= htmlspecialchars($category['category_id']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" action="../../controllers/CategoryController.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                    </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>