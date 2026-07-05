
<?php
// ============================================
// AL-NAAZ FOOD - Send Enquiry API
// ============================================

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$type = sanitize($_POST['type'] ?? 'general');

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

$sql = "INSERT INTO contact_enquiries (name, email, phone, message, type) VALUES (?, ?, ?, ?, ?)";
$result = insertData($sql, [$name, $email, $phone, $message, $type], 'sssss');

if ($result) {
    // Send notification email to admin
    try {
        $admin_email = ADMIN_EMAIL;
        $subject = "New Enquiry from AL-NAAZ FOOD";
        $body = "
        <h2>New Enquiry</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Type:</strong> $type</p>
        <p><strong>Message:</strong><br>$message</p>
        ";
        sendEmail($admin_email, $subject, $body);
    } catch (Exception $e) {
        // Email error, but enquiry saved
    }
    
    echo json_encode(['success' => true, 'message' => 'Enquiry sent successfully! We\'ll get back to you soon.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send enquiry']);
}
?>