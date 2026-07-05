<?php
// ============================================
// AL-NAAZ FOOD - Place Order API
// ============================================

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to place order']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['items']) || empty($data['total'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

$user_id = $_SESSION['user_id'];
$items = $data['items'];
$total = (float)$data['total'];
$advance = min(ADVANCE_AMOUNT, $total);
$delivery_address = sanitize($data['address'] ?? '');
$delivery_type = $data['delivery_type'] ?? 'cod';

// Generate order number
$order_number = generateOrderNumber();

// Insert order
$sql = "INSERT INTO orders (user_id, order_number, total_amount, advance_paid, payment_status, delivery_type, status, delivery_address) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)";
$order_id = insertData($sql, [
    $user_id, 
    $order_number, 
    $total, 
    $advance, 
    $advance > 0 ? 'advance' : 'pending',
    $delivery_type,
    $delivery_address
], 'issdsss');

if ($order_id) {
    // Insert order items
    foreach ($items as $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        insertData($sql, [$order_id, $item['id'], $item['quantity'], $item['price']], 'iiid');
    }
    
    // Clear abandoned orders for this user
    $sql = "DELETE FROM abandoned_orders WHERE user_id = ?";
    executeQuery($sql, [$user_id], 'i');
    
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'order_number' => $order_number
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}
?>