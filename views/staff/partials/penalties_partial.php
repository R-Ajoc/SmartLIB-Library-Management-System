<div class="card shadow rounded-3 mb-2">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title fw-bold text-dark m-0">Penalties Management</h4>
                <form method="GET" class="d-flex" style="gap:10px; max-width:260px;">
                    <input type="text" name="search" 
                        class="form-control"
                        placeholder="Search name..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr class="text-center text-sm text-muted">
                        <th>User</th>
                        <th>Total Penalties</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($penaltySummary)): ?>
                    <?php foreach ($penaltySummary as $penalty): ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars($penalty['full_name']) ?></td>
                            <td><?= number_format($penalty['total_penalties'], 2) ?></td>
                            <td><?= $penalty['last_updated'] ?? '—' ?></td>
                            <td>
                                <?php if ($penalty['unpaid_count'] > 0): ?>
                                    <form method="post" action="<?= $root_path ?>controllers/StaffController.php">
                                        <input type="hidden" name="action" value="mark_all_penalties_paid">
                                        <input type="hidden" name="user_id" value="<?= $penalty['user_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Mark All Paid
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="text-center">
                        <td colspan="4" class="text-muted">No penalty records found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
