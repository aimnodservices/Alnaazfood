<?php
// ============================================
// AL-NAAZ FOOD - Print Order
// ============================================

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isOwner()) {
    die('Unauthorized access');
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    die('Invalid order ID');
}

// Get order details
$order = getRow("
    SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
", [$order_id], 'i');

if (!$order) {
    die('Order not found');
}

// Get order items
$items = getRows("
    SELECT oi.*, p.name as product_name 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
", [$order_id], 'i');

// Get settings
$settings = [];
$settings_result = executeQuery("SELECT setting_key, setting_value FROM website_settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Order - #<?php echo $order['order_number']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: #fff; 
            color: #000; 
            padding: 20px;
            font-size: 14px;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 28px;
            letter-spacing: 2px;
        }
        .header .gold {
            color: #D4AF37;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .order-info .label {
            font-weight: bold;
            color: #666;
        }
        .order-info .value {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background: #f0f0f0;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #000;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        table .total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .payment-info {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #FFF3E0; color: #E65100; }
        .status-confirmed { background: #E3F2FD; color: #0D47A1; }
        .status-processing { background: #F3E5F5; color: #4A148C; }
        .status-shipped { background: #E0F7FA; color: #006064; }
        .status-delivered { background: #E8F5E9; color: #1B5E20; }
        .status-cancelled { background: #FFEBEE; color: #B71C1C; }
        .status-advance { background: #FFF8E1; color: #E65100; }
        .status-full { background: #E8F5E9; color: #1B5E20; }
        
        @media print {
            body { padding: 0; }
            .print-container { border: none; padding: 0; }
            .no-print { display: none; }
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .no-print button {
            padding: 10px 30px;
            background: #D4AF37;
            color: #000;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .no-print button:hover { background: #B8960F; }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">
            <h1>✨ AL-NAAZ <span class="gold">FOOD</span></h1>
            <p><?php echo $settings['contact_address'] ?? ''; ?></p>
            <p>📞 <?php echo $settings['contact_phone'] ?? ''; ?> | ✉️ <?php echo $settings['contact_email'] ?? ''; ?></p>
            <p style="margin-top: 5px;"><strong>Order Invoice</strong></p>
        </div>

        <div class="order-info">
            <div>
                <div><span class="label">Order #:</span> <span class="value"><?php echo $order['order_number']; ?></span></div>
                <div><span class="label">Date:</span> <?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></div>
                <div><span class="label">Customer:</span> <?php echo $order['customer_name'] ?? 'Guest'; ?></div>
            </div>
            <div>
                <div><span class="label">Email:</span> <?php echo $order['customer_email'] ?? 'N/A'; ?></div>
                <div><span class="label">Phone:</span> <?php echo $order['customer_phone'] ?? 'N/A'; ?></div>
                <div><span class="label">Delivery:</span> <?php echo $order['delivery_address'] ?? 'N/A'; ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th style="text-align: right;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($items as $index => $item): 
                    $total = $item['price'] * $item['quantity'];
                    $subtotal += $total;
                ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $item['product_name'] ?? 'Unknown Product'; ?></td>
                        <td style="text-align: right;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Subtotal:</td>
                    <td style="text-align: right;">₹<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                    <td style="text-align: right; font-size: 18px; color: #D4AF37;">
                        <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="payment-info">
            <div>
                <strong>Payment Status:</strong>
                <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                    <?php echo strtoupper($order['payment_status']); ?>
                </span>
            </div>
            <div>
                <strong>Advance Paid:</strong> ₹<?php echo number_format($order['advance_paid'], 2); ?>
            </div>
            <div>
                <strong>Balance Due:</strong> ₹<?php echo number_format($order['total_amount'] - $order['advance_paid'], 2); ?>
            </div>
            <div>
                <strong>Order Status:</strong>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo strtoupper($order['status']); ?>
                </span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing AL-NAAZ FOOD!</p>
            <p>For any queries, contact us at <?php echo $settings['contact_phone'] ?? ''; ?></p>
            <p style="margin-top: 10px; font-size: 10px;">This is a system generated invoice. Valid without signature.</p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Order</button>
        <button onclick="window.close()" style="margin-left: 10px;">Close</button>
    </div>

    <script>
        // Auto print
        window.onload = function() {
            // Comment this to prevent auto print
            // window.print();
        }
    </script>
</body>
</html>