<div class="modal fade" id="reservationsModal" tabindex="-1" aria-labelledby="reservationsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reservationsModalLabel">Active Reservations</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (empty($reservationData)): ?>
          <div class="alert alert-light text-center border">
            <i class="fas fa-info-circle me-2 text-primary"></i>
            No active reservations.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-sm align-middle">
              <thead>
                <tr>
                  <th>Book Title & Author</th>
                  <th>Reservation Date</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservationData as $item): ?>
                  <tr>
                    <td>
                      <div><?= htmlspecialchars($item['title']) ?></div>
                      <div class="text-muted"><?= htmlspecialchars($item['author']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($item['date']) ?></td>
                    <td class="text-center">
                      <?php
                        $badgeClass = match(strtolower($item['status'])) {
                          'pending' => 'bg-warning text-dark',
                          'approved' => 'bg-success text-white',
                          'rejected' => 'bg-danger text-white',
                          'cancelled' => 'bg-secondary text-white',
                          default => 'bg-light text-dark'
                        };
                      ?>
                      <span class="badge <?= $badgeClass ?> rounded-pill"><?= $item['status'] ?></span>
                    </td>
                    <td class="text-center">
                      <?php if (strtolower($item['status']) === 'pending'): ?>
                        <form method="post" action="<?= $root_path ?>controllers/RequestController.php" class="d-inline">
                          <input type="hidden" name="action" value="cancel_request">
                          <input type="hidden" name="request_id" value="<?= $item['request_id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this reservation?')">
                            Cancel
                          </button>
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
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
