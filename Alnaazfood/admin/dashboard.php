<?php
// ============================================
// AL-NAAZ FOOD - Admin Dashboard
// ============================================

require_once __DIR__ . '/../config/config.php';

// Check if user is logged in and is owner
if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'admin.css';
$page_js = 'admin.js';

// Get statistics
$total_visitors = getCount('visitor_analytics');
$total_visitors_today = getCount('visitor_analytics', "DATE(visit_date) = CURDATE()");
$total_products = getCount('products');
$total_orders = getCount('orders');
$total_customers = getCount('users', "role = 'customer'");
$total_reviews = getCount('reviews');

// Get product views
$product_views = getRows("
    SELECT p.name, COUNT(v.id) as views 
    FROM visitor_analytics v 
    LEFT JOIN products p ON v.product_viewed = p.id 
    WHERE v.product_viewed IS NOT NULL 
    GROUP BY v.product_viewed 
    ORDER BY views DESC 
    LIMIT 10
");

// Get abandoned orders
$abandoned = getRows("
    SELECT u.name, u.email, a.amount, a.status, a.created_at 
    FROM abandoned_orders a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC 
    LIMIT 20
");

// Get recent orders
$recent_orders = getRows("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC 
    LIMIT 10
");

// Get best selling products
$best_selling = getRows("
    SELECT p.name, SUM(oi.quantity) as total_sold 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    GROUP BY oi.product_id 
    ORDER BY total_sold DESC 
    LIMIT 5
");

include_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3>👑 AL-NAAZ</h3>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="edit-home.php"><i class="fas fa-home"></i> Edit Home</a></li>
                <li><a href="edit-hero.php"><i class="fas fa-image"></i> Edit Hero</a></li>
                <li><a href="edit-products.php"><i class="fas fa-box"></i> Edit Products</a></li>
                <li><a href="edit-masala.php"><i class="fas fa-pepper-hot"></i> Edit Masala</a></li>
                <li><a href="edit-raw.php"><i class="fas fa-seedling"></i> Edit Raw Materials</a></li>
                <li><a href="edit-dryfruit.php"><i class="fas fa-nut"></i> Edit Dry Fruits</a></li>
                <li><a href="edit-owner.php"><i class="fas fa-user-tie"></i> Edit Owner</a></li>
                <li><a href="edit-settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="<?php echo SITE_URL; ?>erp/dashboard.php"><i class="fas fa-chart-bar"></i> ERP</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Dashboard</h1>
            <div class="admin-user">
                <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                <a href="<?php echo SITE_URL; ?>auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--royal-gold);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_visitors); ?></h3>
                    <p>Total Visitors</p>
                    <small><?php echo number_format($total_visitors_today); ?> today</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(139, 26, 26, 0.1); color: var(--deep-red);">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_products); ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1); color: #4CAF50;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_orders); ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(33, 150, 243, 0.1); color: #2196F3;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_customers); ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 152, 0, 0.1); color: #FF9800;">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_reviews); ?></h3>
                    <p>Total Reviews</p>
                </div>
            </div>
        </div>

        <div class="admin-grid">
            <!-- Best Selling Products -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>🏆 Best Selling Products</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($best_selling)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Total Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($best_selling as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $item['name'] ?? 'Unknown'; ?></td>
                                        <td><?php echo number_format($item['total_sold']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-grey); text-align: center;">No data available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>📦 Recent Orders</h3>
                    <a href="<?php echo SITE_URL; ?>erp/orders.php" class="btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['order_number']; ?></td>
                                        <td><?php echo $order['customer_name'] ?? 'Guest'; ?></td>
                                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-grey); text-align: center;">No orders yet</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Abandoned Orders -->
            <div class="admin-card full-width">
                <div class="card-header">
                    <h3>⚠️ Abandoned Orders (Payment Not Completed)</h3>
                    <span class="badge-danger">For Analysis</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($abandoned)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($abandoned as $item): ?>
                                    <tr>
                                        <td><?php echo $item['name'] ?? 'Guest'; ?></td>
                                        <td><?php echo $item['email'] ?? 'N/A'; ?></td>
                                        <td>₹<?php echo number_format($item['amount'] ?? 0, 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $item['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y, H:i', strtotime($item['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-grey); text-align: center;">No abandoned orders</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Views -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>👁️ Most Viewed Products</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($product_views)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($product_views as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $item['name'] ?? 'Unknown'; ?></td>
                                        <td><?php echo number_format($item['views']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-grey); text-align: center;">No data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>