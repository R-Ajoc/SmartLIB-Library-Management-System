<div class="table-responsive">
  <table class="table table-hover table-striped align-middle">
    <thead class="table-light">
      <tr class="text-center text-sm text-muted">
        <th>User</th>
        <th>Role</th>
        <th>Book Title</th>
        <th>Reserved At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($readyPickups)): ?>
          <?php foreach ($readyPickups as $res): ?>
              <tr class="text-center">
                  <td><?= htmlspecialchars($res['full_name'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars(ucfirst($res['role'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($res['book_title'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($res['reservation_date'] ?? '-') ?></td>
                  <td>
                      <span class="badge bg-info text-dark rounded-pill">
                          <?= ucfirst($res['status'] ?? '-') ?>
                      </span>
                  </td>
                  <td>
                      <form method="post" action="<?= $root_path ?>controllers/StaffController.php" class="d-inline">
                          <input type="hidden" name="action" value="pickup_reservation">
                          <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                          <button type="submit" class="btn btn-sm btn-primary">
                              Pick Up
                          </button>
                      </form>
                  </td>
              </tr>
          <?php endforeach; ?>
      <?php else: ?>
          <tr class="text-center">
              <td colspan="6" class="text-muted">No reservations ready for pickup.</td>
          </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
