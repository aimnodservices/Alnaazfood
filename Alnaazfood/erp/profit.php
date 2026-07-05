<?php
// ============================================
// AL-NAAZ FOOD - Profit Calculator (ERP)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'erp.css';
$page_js = 'erp.js';

// Get date filters
$start_date = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : date('Y-m-d');

// Get revenue
$revenue_result = getRow("
    SELECT SUM(total_amount) as total 
    FROM orders 
    WHERE status != 'cancelled' 
    AND DATE(order_date) BETWEEN ? AND ?
", [$start_date, $end_date], 'ss');
$total_revenue = $revenue_result['total'] ?? 0;

// Get expenses
$expense_result = getRow("
    SELECT SUM(amount) as total 
    FROM erp_expenses 
    WHERE expense_date BETWEEN ? AND ?
", [$start_date, $end_date], 'ss');
$total_expenses = $expense_result['total'] ?? 0;

// Get expenses by type
$expense_breakdown = getRows("
    SELECT type, SUM(amount) as total 
    FROM erp_expenses 
    WHERE expense_date BETWEEN ? AND ?
    GROUP BY type
", [$start_date, $end_date], 'ss');

// Get order count
$order_count = getCount('orders', "status != 'cancelled' AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'");

// Calculate profit
$profit = $total_revenue - $total_expenses;
$profit_margin = $total_revenue > 0 ? ($profit / $total_revenue) * 100 : 0;

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
                <li><a href="order-history.php"><i class="fas fa-history"></i> Order History</a></li>
                <li><a href="expenses.php"><i class="fas fa-money-bill-wave"></i> Expenses</a></li>
                <li><a href="profit.php" class="active"><i class="fas fa-calculator"></i> Profit Calculator</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php"><i class="fas fa-crown"></i> Admin</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>🧮 Profit Calculator</h1>
        </div>

        <!-- Date Filter -->
        <div class="admin-card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3>📅 Select Period</h3>
            </div>
            <div class="card-body">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                    <div>
                        <label style="color: var(--text-grey); font-size: 14px; display: block; margin-bottom: 5px;">Start Date</label>
                        <input type="date" name="start_date" value="<?php echo $start_date; ?>" style="padding: 10px 15px; background: var(--primary-black); border: 1px solid #333; border-radius: 8px; color: var(--text-white);">
                    </div>
                    <div>
                        <label style="color: var(--text-grey); font-size: 14px; display: block; margin-bottom: 5px;">End Date</label>
                        <input type="date" name="end_date" value="<?php echo $end_date; ?>" style="padding: 10px 15px; background: var(--primary-black); border: 1px solid #333; border-radius: 8px; color: var(--text-white);">
                    </div>
                    <div>
                        <button type="submit" class="btn-sm" style="background: var(--royal-gold); color: var(--primary-black); padding: 10px 30px; border: none; border-radius: 8px; cursor: pointer;">Calculate</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profit Cards -->
        <div class="stats-grid" style="margin-bottom: 25px;">
            <div class="stat-card" style="border-color: rgba(76, 175, 80, 0.3);">
                <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1); color: #4CAF50;">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: #4CAF50;">₹<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                    <small><?php echo $order_count; ?> orders</small>
                </div>
            </div>
            <div class="stat-card" style="border-color: rgba(244, 67, 54, 0.3);">
                <div class="stat-icon" style="background: rgba(244, 67, 54, 0.1); color: #F44336;">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: #F44336;">₹<?php echo number_format($total_expenses, 2); ?></h3>
                    <p>Total Expenses</p>
                </div>
            </div>
            <div class="stat-card" style="border-color: <?php echo $profit >= 0 ? 'rgba(76, 175, 80, 0.5)' : 'rgba(244, 67, 54, 0.5)'; ?>;">
                <div class="stat-icon" style="background: <?php echo $profit >= 0 ? 'rgba(76, 175, 80, 0.1)' : 'rgba(244, 67, 54, 0.1)'; ?>; color: <?php echo $profit >= 0 ? '#4CAF50' : '#F44336'; ?>;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: <?php echo $profit >= 0 ? '#4CAF50' : '#F44336'; ?>;">
                        <?php echo $profit >= 0 ? '+' : ''; ?>₹<?php echo number_format($profit, 2); ?>
                    </h3>
                    <p>Net Profit</p>
                    <small>Margin: <?php echo number_format($profit_margin, 2); ?>%</small>
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-header">
                    <h3>📊 Expense Breakdown</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($expense_breakdown)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expense_breakdown as $item): ?>
                                    <tr>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $item['type'])); ?></td>
                                        <td>₹<?php echo number_format($item['total'], 2); ?></td>
                                        <td>
                                            <?php 
                                            $percent = $total_expenses > 0 ? ($item['total'] / $total_expenses) * 100 : 0;
                                            echo number_format($percent, 1); ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background: rgba(212, 175, 55, 0.05);">
                                    <td><strong>Total</strong></td>
                                    <td><strong>₹<?php echo number_format($total_expenses, 2); ?></strong></td>
                                    <td><strong>100%</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-grey); text-align: center;">No expenses in this period</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>📈 Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div style="padding: 15px; background: var(--primary-black); border-radius: 8px; margin-bottom: 10px;">
                        <div style="color: var(--text-grey);">Average Order Value</div>
                        <div style="font-size: 24px; color: var(--royal-gold); font-weight: bold;">
                            ₹<?php echo $order_count > 0 ? number_format($total_revenue / $order_count, 2) : '0.00'; ?>
                        </div>
                    </div>
                    <div style="padding: 15px; background: var(--primary-black); border-radius: 8px; margin-bottom: 10px;">
                        <div style="color: var(--text-grey);">Profit per Order</div>
                        <div style="font-size: 24px; color: <?php echo $profit >= 0 ? '#4CAF50' : '#F44336'; ?>; font-weight: bold;">
                            <?php echo $profit >= 0 ? '+' : ''; ?>₹<?php echo $order_count > 0 ? number_format($profit / $order_count, 2) : '0.00'; ?>
                        </div>
                    </div>
                    <div style="padding: 15px; background: var(--primary-black); border-radius: 8px;">
                        <div style="color: var(--text-grey);">Revenue to Expense Ratio</div>
                        <div style="font-size: 24px; color: var(--royal-gold); font-weight: bold;">
                            <?php echo $total_expenses > 0 ? number_format($total_revenue / $total_expenses, 2) : '∞'; ?>:1
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>