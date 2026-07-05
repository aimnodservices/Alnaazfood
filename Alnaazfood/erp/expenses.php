<?php
// ============================================
// AL-NAAZ FOOD - Expenses Management (ERP)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'erp.css';
$page_js = 'erp.js';

$message = '';
$error = '';

// Get expenses
$expenses = getRows("SELECT * FROM erp_expenses ORDER BY expense_date DESC");

// Handle add expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $type = sanitize($_POST['type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $expense_date = sanitize($_POST['expense_date'] ?? date('Y-m-d'));
    
    if ($type && $amount > 0) {
        $sql = "INSERT INTO erp_expenses (type, description, amount, expense_date) VALUES (?, ?, ?, ?)";
        $result = insertData($sql, [$type, $description, $amount, $expense_date], 'ssds');
        
        if ($result) {
            $message = 'Expense added successfully!';
        } else {
            $error = 'Failed to add expense';
        }
    } else {
        $error = 'Please fill all required fields';
    }
    
    // Refresh expenses
    $expenses = getRows("SELECT * FROM erp_expenses ORDER BY expense_date DESC");
}

// Handle delete expense
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM erp_expenses WHERE id = ?";
    $result = executeQuery($sql, [$id], 'i');
    
    if ($result) {
        $message = 'Expense deleted successfully!';
    } else {
        $error = 'Failed to delete expense';
    }
    
    $expenses = getRows("SELECT * FROM erp_expenses ORDER BY expense_date DESC");
}

// Get totals by type
$totals = [];
$types = ['staff_salary', 'raw_spent', 'bills', 'other'];
foreach ($types as $type) {
    $result = getRow("SELECT SUM(amount) as total FROM erp_expenses WHERE type = ?", [$type], 's');
    $totals[$type] = $result['total'] ?? 0;
}
$grand_total = array_sum($totals);

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
                <li><a href="expenses.php" class="active"><i class="fas fa-money-bill-wave"></i> Expenses</a></li>
                <li><a href="profit.php"><i class="fas fa-calculator"></i> Profit Calculator</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php"><i class="fas fa-crown"></i> Admin</a></li>
                <li><a href="<?php echo SITE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>💰 Expenses Management</h1>
        </div>

        <?php if ($message): ?>
            <div style="background: #1A4A1A; color: #F5F5F5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #8B1A1A; color: #F5F5F5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Summary -->
        <div class="stats-grid" style="margin-bottom: 25px;">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>₹<?php echo number_format($totals['staff_salary'], 2); ?></h3>
                    <p>Staff Salary</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>₹<?php echo number_format($totals['raw_spent'], 2); ?></h3>
                    <p>Raw Materials</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>₹<?php echo number_format($totals['bills'], 2); ?></h3>
                    <p>Bills</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>₹<?php echo number_format($totals['other'], 2); ?></h3>
                    <p>Other Expenses</p>
                </div>
            </div>
            <div class="stat-card" style="border-color: var(--royal-gold);">
                <div class="stat-info">
                    <h3 style="color: var(--royal-gold);">₹<?php echo number_format($grand_total, 2); ?></h3>
                    <p>Total Expenses</p>
                </div>
            </div>
        </div>

        <!-- Add Expense Form -->
        <div class="admin-card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3>➕ Add Expense</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="edit-form">
                    <input type="hidden" name="add_expense" value="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Type *</label>
                            <select name="type" required>
                                <option value="staff_salary">Staff Salary</option>
                                <option value="raw_spent">Raw Material Spent</option>
                                <option value="bills">Bills (Electricity, Water, etc.)</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount (₹) *</label>
                            <input type="number" name="amount" step="0.01" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date *</label>
                            <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" placeholder="Brief description">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">💰 Add Expense</button>
                </form>
            </div>
        </div>

        <!-- Expenses List -->
        <div class="admin-card">
            <div class="card-header">
                <h3>📋 All Expenses</h3>
                <span style="color: var(--text-grey); font-size: 14px;">Total: <?php echo count($expenses); ?></span>
            </div>
            <div class="card-body" style="overflow-x: auto;">
                <?php if (!empty($expenses)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $index => $expense): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="status-badge" style="background: rgba(212, 175, 55, 0.2); color: var(--royal-gold);">
                                            <?php echo ucfirst(str_replace('_', ' ', $expense['type'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $expense['description'] ?? 'N/A'; ?></td>
                                    <td style="color: var(--deep-red); font-weight: bold;">-₹<?php echo number_format($expense['amount'], 2); ?></td>
                                    <td><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $expense['id']; ?>" onclick="return confirm('Delete this expense?')" style="color: var(--deep-red);">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: rgba(212, 175, 55, 0.05);">
                                <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                                <td style="font-weight: bold; color: var(--deep-red);">-₹<?php echo number_format($grand_total, 2); ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-grey); text-align: center;">No expenses recorded yet</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>