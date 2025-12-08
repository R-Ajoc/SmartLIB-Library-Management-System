<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/AuthModel.php';

// Remember Me: Auto-login if session not set but cookie exists
if (!isset($_SESSION['is_logged_in']) && isset($_COOKIE['remember_me_token'])) {
    $authModel = new AuthModel();
    $user = $authModel->getUserByRememberToken($_COOKIE['remember_me_token']);

    if ($user) {
        // Set session for the user
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['firstname'] = $user['firstname'];
    } else {
        // Invalid token, delete cookie
        setcookie('remember_me_token', '', time() - 3600, "/");
    }
}
