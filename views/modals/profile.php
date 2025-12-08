<?php

if (!isset($user) || !$user) {
    echo '<div class="alert alert-danger">User data could not be loaded.</div>';
    return;
}

$user_role = $user['role'] ?? 'user';
$user_id_label = strtoupper($user_role) === 'STUDENT' ? 'Student ID' : (strtoupper($user_role) === 'TEACHER' ? 'Faculty ID' : 'User ID');

if (strtoupper($user_role) === 'STUDENT') {
    $user_bio = $user['bio'] ?? 'Dedicated student navigating academic resources.';
} elseif (strtoupper($user_role) === 'TEACHER') {
    $user_bio = $user['bio'] ?? 'Faculty member committed to research and education.';
} else {
    $user_bio = $user['bio'] ?? 'General library system user.';
}


?>

<main class="container my-5 d-flex flex-column align-items-center">

    <h2 class="profile-title"><i class="fas fa-id-badge"> </i> My Profile Summary</h2>

    <div class="profile-card card shadow-lg mt-3">
        <div class="card-header text-white text-center" style="background-color: #2C3E50">
            <h5 class="mb-0">Personal Information</h5>
        </div>

        <div class="card-body">

            <div class="mb-4 text-center p-3 border rounded bg-light">
                <p class="mb-1 text-muted"><i class="fas fa-info-circle"></i> Profile Description / Bio</p>
                <p class="lead mb-0 fst-italic">"<?= htmlspecialchars($user_bio); ?>"</p>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-bold"><i class="fas fa-signature"></i> First Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['firstname'] ?? ''); ?>" readonly>
                </div>
                <div class="col">
                    <label class="form-label fw-bold">M.I</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['midint'] ?? ''); ?>" readonly>
                </div>
                <div class="col">
                    <label class="form-label fw-bold">Last Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['lastname'] ?? ''); ?>" readonly>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="fas fa-user-tag"></i> Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold"><i class="fas fa-at"></i> Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" readonly>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-briefcase"></i> Role</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($user_role) ?? 'N/A'); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-calendar-alt"></i> Member Since</label>
                    <input type="text" class="form-control" value="<?= isset($user['created_at']) ? date('F d, Y', strtotime($user['created_at'])) : 'N/A'; ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" value="********" readonly>
                <div class="form-text">Use the **Settings** page to update your password or profile details.</div>
            </div>
            
            </div>
    </div>
</main>