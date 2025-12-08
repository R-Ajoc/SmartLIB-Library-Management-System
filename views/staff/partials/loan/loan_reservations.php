<div class="table-responsive">
  <table class="table table-hover table-striped align-middle">
    <thead class="table-light">
      <tr class="text-center text-sm text-muted">
        <th>User</th>
        <th>Role</th>
        <th>Book Title</th>
        <th>Reservation Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($reservations)): ?>
        <?php foreach ($reservations as $res): ?>
          <tr class="text-center">
            <td><?= htmlspecialchars($res['user_name']) ?></td>
            <td><?= htmlspecialchars($res['role']) ?></td>
            <td><?= htmlspecialchars($res['book_title']) ?></td>
            <td><?= $res['reservation_date'] ?></td>
            <td>
              <?php
              $badgeClass = match(strtolower($res['status'])) {
                  'pending' => 'bg-warning text-dark',
                  'ready_for_pickup' => 'bg-info text-white',
                  'approved' => 'bg-success text-white',
                  'rejected' => 'bg-danger text-white',
                  default => 'bg-light text-dark'
              };
              ?>
              <span class="badge <?= $badgeClass ?> rounded-pill"><?= ucfirst($res['status']) ?></span>
            </td>
            <td>
              <?php if (strtolower($res['status']) === 'pending'): ?>
                <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                  <input type="hidden" name="action" value="approve_reservation">
                  <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-success">Approve</button>
                </form>
                <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                  <input type="hidden" name="action" value="reject_reservation">
                  <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                </form>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="text-center">
          <td colspan="6" class="text-muted">No reservations found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
