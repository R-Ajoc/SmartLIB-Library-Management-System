<div class="table-responsive">
  <table class="table table-hover table-striped align-middle">
    <thead class="table-light">
      <tr class="text-center text-sm text-muted">
        <th>User</th>
        <th>Role</th>
        <th>Book Title</th>
        <th>Date Borrowed</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($activeBorrows)): ?>
        <?php foreach ($activeBorrows as $borrow): ?>
          <tr class="text-center">
            <td><?= htmlspecialchars($borrow['full_name']) ?></td>
            <td><?= htmlspecialchars(ucfirst($borrow['role'])) ?></td>
            <td><?= htmlspecialchars($borrow['book_title']) ?></td>
            <td><?= $borrow['borrow_date'] ?></td>
            <td><?= $borrow['due_date'] ?></td>
            <td>
              <span class="badge bg-warning text-dark rounded-pill"><?= ucfirst($borrow['status']) ?></span>
            </td>
            <td>
              <?php if (strtolower($borrow['status']) === 'borrowed'): ?>
                <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                  <input type="hidden" name="action" value="mark_returned">
                  <input type="hidden" name="borrow_id" value="<?= $borrow['borrow_id'] ?>">
                  <input type="hidden" name="user_id" value="<?= $borrow['user_id'] ?>">
                  <input type="hidden" name="book_id" value="<?= $borrow['book_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-success">Mark Returned</button>
                </form>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="text-center">
          <td colspan="7" class="text-muted">No active borrows found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
