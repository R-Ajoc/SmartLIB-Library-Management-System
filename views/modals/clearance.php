<div class="modal fade" id="clearanceModal" tabindex="-1" aria-labelledby="clearanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clearanceModalLabel">Clearance History & Ledger</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Book Title & Author</th>
              <th>Semester</th>
              <th class="text-end">Amount Paid</th>
              <th class="text-center">Resolution</th>
              <th class="text-center">Clearance Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($historyData as $item): ?>
            <tr>
              <td>
                <div><?= htmlspecialchars($item['title']) ?></div>
                <div class="text-muted"><?= htmlspecialchars($item['author']) ?></div>
              </td>
              <td><?= htmlspecialchars($item['semester']) ?></td>
              <td class="text-end">₱ <?= number_format($item['amountPaid'],2) ?></td>
              <td class="text-center"><?= htmlspecialchars($item['resolution']) ?></td>
              <td class="text-center"><?= htmlspecialchars($item['date']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
