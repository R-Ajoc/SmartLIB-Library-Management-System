<div class="row">
           <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Pending Requests
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $pendingCount ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Active Reservations
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $activeReservations ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Overdue Loans
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $overdueLoans ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Total Cleared
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $totalCleared ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Penalties
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $studentsWithPenalties ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>