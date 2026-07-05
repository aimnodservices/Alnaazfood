<?php
// ============================================
// AL-NAAZ FOOD - Check Auth API
// ============================================

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

echo json_encode([
    'loggedIn' => isLoggedIn(),
    'user' => getCurrentUser()
]);
?>