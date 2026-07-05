<?php
// ============================================
// AL-NAAZ FOOD - Order History (ERP)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'erp.css';
$page_js = 'erp.js';

// Get order history with filters
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$filter_date = isset($_GET['date']) ? sanitize($_GET['date']) : '';

$sql = "SELECT o.*, u.name as customer_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
$params = [];
$types = '';

if (!empty($filter_status)) {
    $sql .= " AND o.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($filter_date)) {
    $sql .= " AND DATE(o.order_date) = ?";
    $params[] = $filter_date;
    $types .= 's';
}

$sql .= " ORDER BY o.order_date DESC";

$orders = getRows($sql, $params, $types);

// Get summary stats
$total_orders = count($orders);
$total_revenue = array_sum(array_column($orders, 'total_amount'));
$total_advance = array_sum(array_column($orders, 'advance_paid'));

// Get status counts
$status_counts = [];
$statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
foreach ($statuses as $status) {
    $count = getCount('orders', "status = '$status'");
    $status_counts[$status] = $count;
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
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="order-history.php" class="active"><i class="fas fa-history"></i> Order History</a></li>
                <li><a href="expenses.php"><i class="fas fa-money-bill-wave"></i> Expenses</a></li>
                <li><a href="profit.php"><i class="fas fa-calculator"></i> Profit Calculator</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php"><i class="fas fa-crown"></i> Admin</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>📋 Order History</h1>
        </div>

        <!-- Summary Cards -->
        <div class="stats-grid" style="margin-bottom: 25px;">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>₹<?php echo number_format($total_advance, 2); ?></h3>
                    <p>Total Advance Collected</p>
                </div>
            </div>
        </div>

        <!-- Status Summary -->
        <div class="admin-card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3>📊 Order Status Summary</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <?php foreach ($status_counts as $status => $count): ?>
                        <div style="background: var(--primary-black); padding: 10px 20px; border-radius: 8px; border: 1px solid rgba(212, 175, 55, 0.1);">
                            <span class="status-badge status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
                            <span style="color: var(--text-white); font-weight: bold; margin-left: 10px;"><?php echo $count; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3>🔍 Filter Orders</h3>
            </div>
            <div class="card-body">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                    <div>
                        <label style="color: var(--text-grey); font-size: 14px; display: block; margin-bottom: 5px;">Status</label>
                        <select name="status" style="padding: 10px 15px; background: var(--primary-black); border: 1px solid #333; border-radius: 8px; color: var(--text-white);">
                            <option value="">All</option>
                            <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $filter_status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="processing" <?php echo $filter_status == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $filter_status == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $filter_status == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $filter_status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label style="color: var(--text-grey); font-size: 14px; display: block; margin-bottom: 5px;">Date</label>
                        <input type="date" name="date" value="<?php echo $filter_date; ?>" style="padding: 10px 15px; background: var(--primary-black); border: 1px solid #333; border-radius: 8px; color: var(--text-white);">
                    </div>
                    <div>
                        <button type="submit" class="btn-sm" style="background: var(--royal-gold); color: var(--primary-black); padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer;">Filter</button>
                        <a href="order-history.php" class="btn-sm" style="background: var(--text-grey); color: var(--primary-black); padding: 10px 25px; border: none; border-radius: 8px; text-decoration: none;">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="admin-card">
            <div class="card-header">
                <h3>📋 Orders</h3>
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
                                <th>Advance</th>
                                <th>Payment</th>
                                <th>Status</th>
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
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <a href="print-order.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn-sm" style="background: var(--royal-gold); color: var(--primary-black);">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: rgba(212, 175, 55, 0.05);">
                                <td colspan="2" style="text-align: right; font-weight: bold;">Totals:</td>
                                <td><strong>₹<?php echo number_format($total_revenue, 2); ?></strong></td>
                                <td><strong>₹<?php echo number_format($total_advance, 2); ?></strong></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-grey); text-align: center;">No orders found</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>