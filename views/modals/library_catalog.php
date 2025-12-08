<div class="container-md mt-5 catalog-constrained-width">

    <div class="row mb-2">
        <div class="col-12 text-center">
            <h1 class="catalog-title">Library Catalog</h1>
            <p class="catalog-subtitle text-muted">A World of Knowledge Awaits</p>
        </div>
    </div>

    <form method="GET" action="library_catalog.php">
        <header class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <input type="text" id="search-input" name="search-input" class="form-control form-control-lg" 
                        placeholder="Search for books, authors, or keywords..."
                        value="<?= $searchTerm ?>">
                    
                    <select id="category-filter" name="category-filter" class="form-select" style="max-width: 150px;">
                        <option value="0">All Categories</option>
                        <?php foreach ($allCategories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id']) ?>"
                                <?= ($categoryId === (int)$category['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-dark" id="search-button">Search</button>
                </div>
            </div>
        </header>
    </form>

    <hr>

    <main class="book-results-list row">
        <?php if (empty($books) && $totalBooksCount > 0): ?>
            <div class="col-12">
                <p class="text-center text-muted mt-5">No books found on this page. Try navigating back or clearing your filter.</p>
            </div>
        <?php elseif (empty($books) && $totalBooksCount === 0): ?>
            <div class="col-12">
                <p class="text-center text-muted mt-5">No books found matching your criteria.</p>
            </div>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <?php $imageUrl = createPlaceholderURL($book['title']); ?>
                <div class="col-12">
                    <div class="book-item d-flex border rounded p-3 mb-2 bg-white shadow-sm" data-book-id="<?= htmlspecialchars($book['book_id']) ?>">
                        
                        <div class="book-left me-3 text-center">
                            <img class="book-cover mb-2" src="<?= $imageUrl ?>" alt="Cover for <?= htmlspecialchars($book['title']) ?>" style="width: 100px; height: 140px;">
                            
                            <!-- Borrow Form -->
                            <form action="<?= $root_path ?>controllers/RequestController.php" method="POST" class="d-grid gap-1">
                                <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['book_id']) ?>">
                                <button type="submit" name="action" value="borrow_request" 
                                    class="btn btn-sm btn-success" 
                                    <?= $is_limit_reached ? 'disabled' : '' ?>>
                                    <?= $is_limit_reached ? 'Limit Reached' : 'Borrow' ?>
                                </button>
                                <?php if ($is_limit_reached): ?>
                                    <small class="text-danger">Return books to borrow again.</small>
                                <?php endif; ?>
                            </form>
                            
                            <!-- Reserve Form -->
                            <form action="<?= $root_path ?>controllers/RequestController.php" method="POST" class="d-grid gap-1 mt-2">
                                <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['book_id']) ?>">
                                <button type="submit" name="action" value="reserve_request" 
                                    class="btn btn-sm btn-warning" 
                                    <?= $is_reservation_limit_reached ? 'disabled title="Reservation Limit Reached"' : '' ?>>
                                    <?= $is_reservation_limit_reached ? 'Maxed Out' : 'Reserve' ?>
                                </button>
                                <?php if ($is_reservation_limit_reached): ?>
                                    <small class="text-danger">Return books to reserve again.</small>
                                <?php endif; ?>
                            </form>

                            <small class="text-muted mt-1">Available: <?= htmlspecialchars($book['copies_available']) ?></small>
                        </div>
                        
                        <div class="book-details flex-grow-1">
                            <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                            <p class="book-meta">
                                Published: <span class="published-year"><?= htmlspecialchars($book['year_published']) ?></span> 
                                | Author: <span class="author-name"><?= htmlspecialchars($book['author']) ?></span>
                                | Category: <span class="category-name"><?= htmlspecialchars($book['category_name']) ?></span>
                            </p>
                            <p class="book-description"><?= htmlspecialchars($book['description']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php if ($totalPages > 1): ?>
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                        // Build a base query string to preserve search and category filters across pages
                        $baseQuery = http_build_query([
                            'search-input' => $searchTerm,
                            'category-filter' => $categoryId
                        ]);
                        ?>

                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" 
                                 href="?<?= $baseQuery ?>&page=<?= $currentPage - 1 ?>" 
                                 aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php 
                        // Render page links
                        $start = max(1, $currentPage - 2);
                        $end = min($totalPages, $currentPage + 2);

                        if ($start > 1) { echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; }

                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= $baseQuery ?>&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end < $totalPages) { echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; } ?>

                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" 
                                 href="?<?= $baseQuery ?>&page=<?= $currentPage + 1 ?>" 
                                 aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>