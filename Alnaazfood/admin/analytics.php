<?php
// ============================================
// AL-NAAZ FOOD - Analytics (Admin)
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    redirect('auth/login.php');
}

$page_css = 'admin.css';
$page_js = 'analytics.js';

// Get visitor analytics
$total_visitors = getCount('visitor_analytics');
$visitors_today = getCount('visitor_analytics', "DATE(visit_date) = CURDATE()");
$visitors_week = getCount('visitor_analytics', "visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$visitors_month = getCount('visitor_analytics', "visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");

// Daily visitors for chart
$daily_visitors = getRows("
    SELECT 
        DATE(visit_date) as date,
        COUNT(*) as count,
        COUNT(DISTINCT session_id) as unique_visitors
    FROM visitor_analytics 
    WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(visit_date)
    ORDER BY date ASC
");

// Most visited pages
$page_views = getRows("
    SELECT 
        page_visited,
        COUNT(*) as views,
        COUNT(DISTINCT session_id) as unique_visitors
    FROM visitor_analytics 
    GROUP BY page_visited 
    ORDER BY views DESC 
    LIMIT 10
");

// Product views
$product_views = getRows("
    SELECT 
        p.name as product_name,
        COUNT(v.id) as views,
        COUNT(DISTINCT v.session_id) as unique_visitors
    FROM visitor_analytics v 
    LEFT JOIN products p ON v.product_viewed = p.id 
    WHERE v.product_viewed IS NOT NULL
    GROUP BY v.product_viewed 
    ORDER BY views DESC 
    LIMIT 10
");

include_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3>👑 AL-NAAZ</h3>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="analytics.php" class="active"><i class="fas fa-chart-line"></i> Analytics</a></li>
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

    <main class="admin-main">
        <div class="admin-header">
            <h1>📊 Analytics</h1>
        </div>

        <!-- Visitor Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo number_format($total_visitors); ?></h3>
                    <p>Total Visitors</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo number_format($visitors_today); ?></h3>
                    <p>Today</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo number_format($visitors_week); ?></h3>
                    <p>Last 7 Days</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo number_format($visitors_month); ?></h3>
                    <p>Last 30 Days</p>
                </div>
            </div>
        </div>

        <!-- Visitor Chart -->
        <div class="admin-card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3>📈 Daily Visitors (Last 30 Days)</h3>
            </div>
            <div class="card-body">
                <canvas id="visitorChart"></canvas>
            </div>
        </div>

        <div class="admin-grid">
            <!-- Most Visited Pages -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>📄 Most Visited Pages</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($page_views)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Views</th>
                                    <th>Unique</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($page_views as $page): ?>
                                    <tr>
                                        <td><?php echo ucfirst($page['page_visited']); ?></td>
                                        <td><?php echo number_format($page['views']); ?></td>
                                        <td><?php echo number_format($page['unique_visitors']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: var(--text-grey); text-align: center;">No data available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Most Viewed Products -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>👁️ Most Viewed Products</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($product_views)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Views</th>
                                    <th>Unique</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($product_views as $item): ?>
                                    <tr>
                                        <td><?php echo $item['product_name'] ?? 'Unknown'; ?></td>
                                        <td><?php echo number_format($item['views']); ?></td>
                                        <td><?php echo number_format($item['unique_visitors']); ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('visitorChart').getContext('2d');
    
    const dates = <?php echo json_encode(array_column($daily_visitors, 'date')); ?>;
    const counts = <?php echo json_encode(array_column($daily_visitors, 'count')); ?>;
    const uniques = <?php echo json_encode(array_column($daily_visitors, 'unique_visitors')); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.length ? dates : ['No Data'],
            datasets: [
                {
                    label: 'Total Views',
                    data: dates.length ? counts : [0],
                    borderColor: 'rgba(212, 175, 55, 1)',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Unique Visitors',
                    data: dates.length ? uniques : [0],
                    borderColor: 'rgba(139, 26, 26, 1)',
                    backgroundColor: 'rgba(139, 26, 26, 0.1)',
                    fill: true,
                    tension: 0.4
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