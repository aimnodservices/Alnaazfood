<?php
// ============================================
// AL-NAAZ FOOD - Catering Booking API
// ============================================

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$event_type = sanitize($_POST['event_type'] ?? '');
$event_date = sanitize($_POST['event_date'] ?? '');
$guest_count = (int)($_POST['guest_count'] ?? 0);
$special_requirements = sanitize($_POST['special_requirements'] ?? '');

if (empty($name) || empty($email) || empty($phone) || empty($event_type) || empty($event_date)) {
    $_SESSION['flash_message'] = 'Please fill all required fields';
    $_SESSION['flash_type'] = 'error';
    redirect('pages/catering.php');
}

$sql = "INSERT INTO catering_bookings (name, email, phone, event_type, event_date, guest_count, special_requirements) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$result = insertData($sql, [$name, $email, $phone, $event_type, $event_date, $guest_count, $special_requirements], 'sssssis');

if ($result) {
    // Send notification
    try {
        $admin_email = ADMIN_EMAIL;
        $subject = "New Catering Booking from AL-NAAZ FOOD";
        $body = "
        <h2>New Catering Booking</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Event Type:</strong> $event_type</p>
        <p><strong>Event Date:</strong> $event_date</p>
        <p><strong>Guests:</strong> $guest_count</p>
        <p><strong>Special Requirements:</strong><br>$special_requirements</p>
        ";
        sendEmail($admin_email, $subject, $body);
    } catch (Exception $e) {}
    
    $_SESSION['flash_message'] = 'Booking submitted successfully! We\'ll contact you within 24 hours.';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_message'] = 'Failed to submit booking. Please try again.';
    $_SESSION['flash_type'] = 'error';
}

redirect('pages/catering.php');
?>