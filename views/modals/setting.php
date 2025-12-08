<?php

if (!isset($user) || !isset($root_path) || !isset($userId)) {
    echo '<div class="alert alert-danger">Configuration error: Required user data is missing.</div>';
    return;
}
?>


<div class="container settings-container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><i class="fas fa-cog me-2"></i> Account Settings</h1>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['message']); 
            unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>


    <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                <i class="fas fa-user-edit me-1"></i> General Information
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                <i class="fas fa-lock me-1"></i> Change Password
            </button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabContent">
        
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Update Account Details</h5>
                    <form action="<?= $root_path ?>controllers/AuthController.php?action=update_profile" method="POST">
                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                        
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="inputEmail" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="inputFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="inputFirstName" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                            </div>
                             <div class="col-md-2 mb-3">
                                <label for="inputMidInt" class="form-label">M.I</label>
                                <input type="text" class="form-control" id="inputMidInt" name="midint" value="<?= htmlspecialchars($user['midint']) ?>" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="inputLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="inputLastName" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                             <label for="inputUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="inputUsername" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        
                        
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Change Your Password</h5>
                    <form action="<?= $root_path ?>controllers/AuthController.php?action=change_password" method="POST">
                        <input type="hidden" name="user_id" value="<?= $userId ?>">

                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning"><i class="fas fa-key me-1"></i> Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('#password-tab').addEventListener('click', function() {
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    });
</script>