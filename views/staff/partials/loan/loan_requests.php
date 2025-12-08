<div class="table-responsive">
  <table class="table table-hover table-striped align-middle">
    <thead class="table-light">
      <tr class="text-center text-sm text-muted">
        <th>User</th>
        <th>Role</th>
        <th>Book Title</th>
        <th>Date Requested</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($borrowRequests)): ?>
        <?php foreach ($borrowRequests as $req): ?>
          <tr class="text-center">
            <td><?= htmlspecialchars($req['user_name']) ?></td>
            <td><?= htmlspecialchars($req['role']) ?></td>
            <td><?= htmlspecialchars($req['book_title']) ?></td>
            <td><?= $req['request_date'] ?></td>
            <td>
              <?php
              $badgeClass = match(strtolower($req['status'])) {
                  'pending' => 'bg-warning text-dark',
                  'approved' => 'bg-success text-white',
                  'rejected' => 'bg-danger text-white',
                  default => 'bg-light text-dark'
              };
              ?>
              <span class="badge <?= $badgeClass ?> rounded-pill"><?= ucfirst($req['status']) ?></span>
            </td>
            <td>
              <?php if (strtolower($req['status']) === 'pending'): ?>
                <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                  <input type="hidden" name="action" value="approve_borrow">
                  <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-success">Approve</button>
                </form>
                <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                  <input type="hidden" name="action" value="reject_borrow">
                  <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-warning">Reject</button>
                </form>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="text-center">
          <td colspan="6" class="text-muted">No borrow requests found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


