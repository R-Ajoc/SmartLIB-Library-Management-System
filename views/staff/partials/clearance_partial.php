<form method="POST" action="<?= $root_path ?>controllers/StaffController.php" class="mb-3 p-2 border rounded bg-light d-flex justify-content-between align-items-center">
    <div style="color: #2C3E50; font-size: 0.85rem; font-weight: 600;">
        ⚠️ NOTE: Click <u>Set All Penalties</u> after the official clearance week ends.
    </div>

    <input type="hidden" name="action" value="set_all_penalties">

    <button type="submit"
            style="background-color: #DAA520; color: #2C3E50; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;">
        <i class="bi bi-exclamation-circle-fill me-1"></i> Set All Penalties
    </button>
</form>


<div class="card shadow rounded-3 mb-2">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Page Title -->
            <h4 class="card-title fw-bold text-dark m-0">User Clearance</h4>

            <!-- Search Bar -->
            <form method="GET" class="d-flex" style="gap:10px; max-width:260px;">
                <input type="text" name="search" 
                       class="form-control"
                       placeholder="Search name..."
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </form>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="clearanceTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="students-tab" data-bs-toggle="tab" 
                        data-bs-target="#students" type="button" role="tab">Students</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="teachers-tab" data-bs-toggle="tab" 
                        data-bs-target="#teachers" type="button" role="tab">Teachers</button>
            </li>
        </ul>

        <div class="tab-content" id="clearanceTabsContent">

            <!-- Students Tab -->
            <div class="tab-pane fade show active" id="students" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr class="text-center text-sm text-muted">
                                <th>User</th>
                                <th>Status</th>
                                <th>Reason / Missing Items</th>
                                <th>Penalty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clearance->getUsersForClearance('student') as $user): ?>
                                <?php
                                    // Unreturned books
                                    $unreturned = $clearance->getUnreturnedBooks($user['user_id']);
                                    $overdueBooks = array_filter($unreturned, fn($b) => strtotime($b['due_date']) < time() && strtolower($b['status']) === 'borrowed');
                                    $lostBooks    = array_filter($unreturned, fn($b) => strtolower($b['status']) === 'lost');

                                    // Reason text
                                    if (!empty($lostBooks)) {
                                        $reasonText = "Student has lost books.";
                                    } elseif (!empty($overdueBooks)) {
                                        $reasonText = "Student has overdue books.";
                                    } elseif (!empty($unreturned)) {
                                        $reasonText = "Student has unreturned books (not yet due).";
                                    } else {
                                        $reasonText = "Cleared. No missing items.";
                                    }

                                    // Compute penalty (only for overdue or lost books)
                                    $penalty = 0;
                                    foreach ($overdueBooks as $b) { $penalty += $b['price']; }
                                    foreach ($lostBooks as $b) { $penalty += $b['price']; }

                                    // Can clear action
                                    $canClear = empty($unreturned) && strtolower($user['clearance_status'] ?? 'pending') !== 'cleared';
                                ?>
                                <tr class="text-center">
                                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td>
                                        <span class="badge <?= strtolower($user['clearance_status']) === 'cleared' ? 'bg-success text-white' : 'bg-warning text-dark' ?>">
                                            <?= ucfirst($user['clearance_status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($reasonText) ?></td>
                                    <td><?= $penalty > 0 ? "₱{$penalty}" : '-' ?></td>
                                    <td>
                                        <?php if ($canClear): ?>
                                            <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                                                <input type="hidden" name="action" value="clear_user">
                                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Clear</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Teachers Tab -->
            <div class="tab-pane fade" id="teachers" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr class="text-center text-sm text-muted">
                                <th>User</th>
                                <th>Status</th>
                                <th>Reason / Missing Items</th>
                                <th>Penalty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clearance->getUsersForClearance('teacher') as $user): ?>
                                <?php
                                    $unreturned = $clearance->getUnreturnedBooks($user['user_id']);
                                    $overdueBooks = array_filter($unreturned, fn($b) => strtotime($b['due_date']) < time() && strtolower($b['status']) === 'borrowed');
                                    $lostBooks    = array_filter($unreturned, fn($b) => strtolower($b['status']) === 'lost');

                                    if (!empty($lostBooks)) {
                                        $reasonText = "Teacher has lost books.";
                                    } elseif (!empty($overdueBooks)) {
                                        $reasonText = "Teacher has overdue books.";
                                    } elseif (!empty($unreturned)) {
                                        $reasonText = "Teacher has unreturned books (not yet due).";
                                    } else {
                                        $reasonText = "Cleared. No missing items.";
                                    }

                                    $penalty = 0;
                                    foreach ($overdueBooks as $b) { $penalty += $b['price']; }
                                    foreach ($lostBooks as $b) { $penalty += $b['price']; }

                                    $canClear = empty($unreturned) && strtolower($user['clearance_status'] ?? 'pending') !== 'cleared';
                                ?>
                                <tr class="text-center">
                                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td>
                                        <span class="badge <?= strtolower($user['clearance_status']) === 'cleared' ? 'bg-success text-white' : 'bg-warning text-dark' ?>">
                                            <?= ucfirst($user['clearance_status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($reasonText) ?></td>
                                    <td><?= $penalty > 0 ? "₱{$penalty}" : '-' ?></td>
                                    <td>
                                        <?php if ($canClear): ?>
                                            <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                                                <input type="hidden" name="action" value="clear_user">
                                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Clear</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
