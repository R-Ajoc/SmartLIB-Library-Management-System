<?php

function sanitizeInput($data) {
    if (is_array($data)) {
        // Recursively sanitize arrays if needed, but for forms, it typically expect strings
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>