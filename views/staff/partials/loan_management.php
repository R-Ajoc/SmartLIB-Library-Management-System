<div class="card shadow rounded-3 mb-2">
  <div class="card-body p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="card-title fw-bold text-dark m-0">Loan Management</h4>
        <form method="GET" class="d-flex" style="gap:10px; max-width:260px;">
          <input type="text" name="search" 
              class="form-control"
              placeholder="Search name..."
              value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </form>
    </div>

    <div class="d-flex align-items-start">

      <!-- Sidebar -->
      <div class="flex-shrink-0 me-4" style="min-width: 200px;">
        <div class="list-group" id="loanSideNav">
          <button class="list-group-item list-group-item-action active" data-target="requests">
            Borrow Requests
            <?php if($borrowRequestsCount > 0): ?>
              <span class="badge bg-danger ms-2"><?= $borrowRequestsCount ?></span>
            <?php endif; ?>
          </button>
          <button class="list-group-item list-group-item-action" data-target="reservations">
            Reservation Requests
            <?php if($reservationsCount > 0): ?>
              <span class="badge bg-danger ms-2"><?= $reservationsCount ?></span>
            <?php endif; ?>
          </button>
          <button class="list-group-item list-group-item-action" data-target="returnManagement">
            Return Management
          </button>
          <button class="list-group-item list-group-item-action" data-target="pickups">
            Reservation Pickups
          </button>
        </div>
      </div>

      <!-- Content area -->
      <div class="flex-grow-1" style="min-width: 0;"> 
        <div id="requests" class="loan-section">
          <?php include __DIR__ . '/loan/loan_requests.php'; ?>
        </div>
        <div id="reservations" class="loan-section" style="display:none;">
          <?php include __DIR__ . '/loan/loan_reservations.php'; ?>
        </div>
        <div id="returnManagement" class="loan-section" style="display:none;">
          <?php include __DIR__ . '/loan/loan_return_management.php'; ?>
        </div>
        <div id="pickups" class="loan-section" style="display:none;">
          <?php include __DIR__ . '/loan/loan_pickups.php'; ?>
        </div>
      </div>

    </div>
  </div>
</div>


<script>
  const buttons = document.querySelectorAll('#loanSideNav button');
  const sections = document.querySelectorAll('.loan-section');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Activate clicked button
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Show only selected section
      const target = btn.getAttribute('data-target');
      sections.forEach(s => {
        s.style.display = (s.id === target) ? 'block' : 'none';
      });
    });
  });
</script>
