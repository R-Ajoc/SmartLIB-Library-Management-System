<?php
session_start();
session_unset();
session_destroy();

if (isset($_COOKIE['userID'])) {
    setcookie('userID', '', time() - 3600, '/');
}

echo "✅ Session and cookies cleared successfully. <a href='login.php'>Go to Login</a>";
?>
