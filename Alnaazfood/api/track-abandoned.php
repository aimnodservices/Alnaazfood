<?php
// ============================================
// AL-NAAZ FOOD - Track Abandoned Orders API
// ============================================

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['product_id']) || empty($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
$product_id = (int)$data['product_id'];
$status = sanitize($data['status']);
$quantity = (int)($data['quantity'] ?? 1);
$amount = (float)($data['amount'] ?? 0);

// Check if abandoned record exists
$existing = getRow("SELECT id FROM abandoned_orders WHERE user_id = ? AND product_id = ? AND status != 'payment_failed'", [$user_id, $product_id], 'ii');

if ($existing) {
    $sql = "UPDATE abandoned_orders SET status = ?, quantity = ?, amount = ? WHERE id = ?";
    executeQuery($sql, [$status, $quantity, $amount, $existing['id']], 'sidi');
} else {
    $sql = "INSERT INTO abandoned_orders (user_id, product_id, quantity, amount, status) VALUES (?, ?, ?, ?, ?)";
    insertData($sql, [$user_id, $product_id, $quantity, $amount, $status], 'iiids');
}

echo json_encode(['success' => true]);
?>