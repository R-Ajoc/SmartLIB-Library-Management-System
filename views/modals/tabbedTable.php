<div class="card shadow rounded-3 mb-2">
    <div class="card-body p-4">
        <h4 class="card-title fw-bold mb-4 text-dark">Borrowed Books</h4>

        <ul class="nav nav-tabs mb-3" id="loanTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="sem1-tab" data-bs-toggle="tab" data-bs-target="#sem1" type="button" role="tab" aria-controls="sem1" aria-selected="true">
                    Semester 1 
                    <?php if($outstandingCount > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $outstandingCount ?> </span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sem2-tab" data-bs-toggle="tab" data-bs-target="#sem2" type="button" role="tab" aria-controls="sem2" aria-selected="false">
                    Semester 2
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab" aria-controls="requests" aria-selected="false">
                    Book Requests
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="sem1" role="tabpanel">
                <?php if($outstandingCount > 0): ?>
                <?php endif; ?>

                <?php if(empty($loanData)): ?>
                    <div class="alert alert-light text-center border">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        No active loans in Semester 1. Your clearance is up-to-date for this period.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr class="text-uppercase small text-muted">
                                    <th>Book Title</th>
                                    <th>Borrowed On</th>
                                    <th>Due Date</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($loanData as $loan): ?>
                                    <tr>
                                        <td class="fw-medium text-dark"><?= htmlspecialchars($loan['title']) ?></td> 
                                        <td class="text-muted small"><?= $loan['borrowed'] ?></td>
                                        <td class="<?= $loan['isOverdue'] ? 'text-muted small' : 'text-muted small' ?>">
                                            <?= $loan['due'] ?>
                                        </td>
                                        <td> <?= number_format($loan['price'], 2) ?> </td>
                                        <td>
                                            <?php if($loan['isOverdue']): ?>
                                                <span class="badge bg-danger text-white rounded-pill">OVERDUE</span>
                                            <?php elseif($loan['borrowed']): ?>
                                                <span class="badge bg-success text-white rounded-pill">BORROWED</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-white rounded-pill">RETURNED</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="sem2" role="tabpanel">
                <div class="alert alert-blocked py-2">
                    <i class="fas fa-lock me-2"></i>
                    Library services for this semester are currently blocked until all Semester 1 items are cleared.
                </div>
            </div>

            <div class="tab-pane fade" id="requests" role="tabpanel">
                <?php if(empty($requestData)): ?>
                    <div class="alert alert-light text-center border">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        No borrow requests yet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr class="text-uppercase small text-muted">
                                    <th>Book Title</th>
                                    <th>Requested On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($requestData as $req): ?>
                                    <tr>
                                        <td class="fw-medium text-dark"><?= htmlspecialchars($req['title']) ?></td> 
                                        <td class="text-muted small"><?= $req['request_date'] ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = match(strtolower($req['status'])) {
                                                'pending' => 'bg-warning text-dark',
                                                'approved' => 'bg-success text-white',
                                                'rejected' => 'bg-danger text-white',
                                                'cancelled' => 'bg-secondary text-white',
                                                default => 'bg-light text-dark'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= $req['status'] ?></span>
                                        </td>
                                        <td>
                                            <?php if (strtolower($req['status']) === 'pending'): ?>
                                            <form method="post" action="<?= $root_path ?>controllers/RequestController.php" class="d-inline">
                                                <input type="hidden" name="action" value="cancel_request">
                                                <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this request?')">
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


