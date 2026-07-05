<?php
// ============================================
// AL-NAAZ FOOD - Seasonal Offers Page
// ============================================

require_once __DIR__ . '/../config/config.php';

$page_css = 'products.css';
$page_js = 'products.js';

// Get seasonal offers
$offers = getRows("SELECT * FROM seasonal_offers WHERE is_active = TRUE ORDER BY start_date DESC");

// Track visitor
$ip = getClientIP();
$session_id = session_id();
$sql = "INSERT INTO visitor_analytics (ip_address, session_id, page_visited) VALUES (?, ?, ?)";
insertData($sql, [$ip, $session_id, 'seasonal_offers'], 'sss');

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner" style="background: linear-gradient(135deg, #1A0A0A, #8B1A1A);">
    <div class="container">
        <h1>🎁 Seasonal Offers</h1>
        <p>Special festive deals and exclusive discounts</p>
    </div>
</section>

<!-- Active Offers -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>🔥 Active Offers</h2>
            <p>Grab these limited-time offers before they expire!</p>
        </div>
        <?php if (!empty($offers)): ?>
            <div class="offers-grid">
                <?php foreach ($offers as $offer): ?>
                    <div class="offer-card">
                        <div class="offer-image">
                            <img src="<?php echo UPLOAD_URL . ($offer['image'] ?: 'placeholder.jpg'); ?>" 
                                 alt="<?php echo $offer['title']; ?>"
                                 onerror="this.src='https://via.placeholder.com/600x400/1A1A1A/D4AF37?text=AL-NAAZ'">
                            <div class="offer-badge">
                                <span class="discount-badge"><?php echo $offer['discount_percent']; ?>% OFF</span>
                            </div>
                        </div>
                        <div class="offer-info">
                            <h3><?php echo $offer['title']; ?></h3>
                            <p><?php echo $offer['description']; ?></p>
                            <div class="offer-meta">
                                <span><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($offer['start_date'])); ?> - <?php echo date('d M Y', strtotime($offer['end_date'])); ?></span>
                                <?php 
                                $today = time();
                                $end = strtotime($offer['end_date']);
                                $days_left = ceil(($end - $today) / (60 * 60 * 24));
                                if ($days_left > 0):
                                ?>
                                <span class="days-left"><i class="fas fa-clock"></i> <?php echo $days_left; ?> days left</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo SITE_URL; ?>pages/products.php?category=seasonal" class="btn-primary">
                                <i class="fas fa-shopping-bag"></i> Shop Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🎁</div>
                <h3 style="color: var(--text-grey);">No active offers at the moment</h3>
                <p style="color: var(--text-grey); margin-top: 10px;">Check back soon for new seasonal deals!</p>
                <a href="<?php echo SITE_URL; ?>pages/products.php" class="btn-primary" style="margin-top: 20px;">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Subscribe for Updates -->
<section class="section section-dark">
    <div class="container">
        <div class="subscribe-box">
            <h2>📧 Get Offer Updates</h2>
            <p>Subscribe to get notified about new offers and discounts</p>
            <form action="<?php echo SITE_URL; ?>api/subscribe.php" method="POST" class="subscribe-form">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" class="btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>