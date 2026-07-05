<?php
// ============================================
// AL-NAAZ FOOD - ERP Dashboard
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'erp.css';
$page_js = 'erp.js';

// Get ERP stats
$total_orders = getCount('orders');
$total_revenue = getRows("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
$total_revenue = $total_revenue[0]['total'] ?? 0;

// Get monthly revenue
$monthly_revenue = getRows("
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m') as month,
        SUM(total_amount) as revenue,
        COUNT(*) as orders
    FROM orders 
    WHERE status != 'cancelled'
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
");

// Get pending orders
$pending_orders = getCount('orders', "status = 'pending'");

// Get expenses
$total_expenses = getRows("SELECT SUM(amount) as total FROM erp_expenses");
$total_expenses = $total_expenses[0]['total'] ?? 0;

$profit = $total_revenue - $total_expenses;

// Get recent orders for printing
$recent_orders = getRows("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC 
    LIMIT 10
");

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
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-bar"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
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
            <h1>📊 ERP Dashboard</h1>
            <div class="admin-user">
                <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--royal-gold);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_orders); ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1); color: #4CAF50;">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 152, 0, 0.1); color: #FF9800;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($pending_orders); ?></h3>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(244, 67, 54, 0.1); color: #F44336;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>₹<?php echo number_format($total_expenses, 2); ?></h3>
                    <p>Total Expenses</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(33, 150, 243, 0.1); color: #2196F3;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3>₹<?php echo number_format($profit, 2); ?></h3>
                    <p>Net Profit</p>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="admin-card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3>📈 Monthly Revenue</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="admin-card">
            <div class="card-header">
                <h3>📦 Recent Orders</h3>
                <a href="orders.php" class="btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_orders)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td><?php echo $order['customer_name'] ?? 'Guest'; ?></td>
                                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
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
                    <p style="color: var(--text-grey); text-align: center;">No orders yet</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const months = <?php echo json_encode(array_column(array_reverse($monthly_revenue), 'month')); ?>;
    const revenues = <?php echo json_encode(array_column(array_reverse($monthly_revenue), 'revenue')); ?>;
    const orders = <?php echo json_encode(array_column(array_reverse($monthly_revenue), 'orders')); ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months.length ? months : ['No Data'],
            datasets: [
                {
                    label: 'Revenue (₹)',
                    data: months.length ? revenues : [0],
                    backgroundColor: 'rgba(212, 175, 55, 0.5)',
                    borderColor: 'rgba(212, 175, 55, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Orders',
                    data: months.length ? orders : [0],
                    backgroundColor: 'rgba(139, 26, 26, 0.5)',
                    borderColor: 'rgba(139, 26, 26, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#F5F5F5'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#888'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#888'
                    }
                }
            }
        }
    });
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>