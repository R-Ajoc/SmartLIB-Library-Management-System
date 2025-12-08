<div class="card shadow rounded-3 mb-2">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title fw-bold text-dark m-0">Borrower's Management</h4>

            <form method="GET" class="d-flex" style="gap:10px; max-width:260px;">
                <input type="text" name="search" 
                       class="form-control" 
                       placeholder="Search name..."
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr class="text-center text-sm text-muted">
                        <th>User</th>
                        <th>Role</th>
                        <th>Total Borrowed</th>
                        <th>Returned</th>
                        <th>Overdue</th>
                        <th>Last Borrowed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($displaySummary)): ?>
                    <?php foreach ($displaySummary as $row): ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?? 'N/A' ?></td>
                            <td><?= $row['total_borrowed'] ?></td>
                            <td><?= $row['total_returned'] ?></td>
                            <td><?= $row['overdue_count'] ?></td>
                            <td><?= $row['last_borrowed'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning view-history-btn" 
                                        data-user-id="<?= $row['user_id'] ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#borrowHistoryModal">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="text-center">
                        <td colspan="6" class="text-muted">No borrow records found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
