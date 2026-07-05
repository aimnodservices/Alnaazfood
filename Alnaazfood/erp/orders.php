<?php
// ============================================
// AL-NAAZ FOOD - ERP Orders Management
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'erp.css';
$page_js = 'erp.js';

// Get all orders
$orders = getRows("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC
");

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status'] ?? '');
    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $result = executeQuery($sql, [$status, $order_id], 'si');
    
    if ($result) {
        $message = 'Order status updated!';
    } else {
        $error = 'Failed to update order status';
    }
    
    // Refresh orders
    $orders = getRows("
        SELECT o.*, u.name as customer_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC
    ");
}

include_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3>📊 AL-NAAZ</h3>
            <p>ERP System</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Dashboard</a></li>
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="order-history.php"><i class="fas fa-history"></i> Order History</a></li>
                <li><a href="expenses.php"><i class="fas fa-money-bill-wave"></i> Expenses</a></li>
                <li><a href="profit.php"><i class="fas fa-calculator"></i> Profit Calculator</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php"><i class="fas fa-crown"></i> Admin</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>📦 Orders Management</h1>
        </div>

        <?php if (isset($message)): ?>
            <div style="background: #1A4A1A; color: #F5F5F5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div style="background: #8B1A1A; color: #F5F5F5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="card-header">
                <h3>All Orders</h3>
                <span style="color: var(--text-grey); font-size: 14px;">Total: <?php echo count($orders); ?></span>
            </div>
            <div class="card-body" style="overflow-x: auto;">
                <?php if (!empty($orders)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Advance Paid</th>
                                <th>Payment Status</th>
                                <th>Order Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong><?php echo $order['order_number']; ?></strong></td>
                                    <td><?php echo $order['customer_name'] ?? 'Guest'; ?></td>
                                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>₹<?php echo number_format($order['advance_paid'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                            <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="background: var(--primary-black); color: var(--text-white); border: 1px solid #333; border-radius: 5px; padding: 3px 8px;">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <a href="print-order.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn-sm" style="background: var(--royal-gold); color: var(--primary-black);">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-grey); text-align: center;">No orders found</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>