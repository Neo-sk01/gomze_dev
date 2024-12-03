<?php
session_start();
$address = "info@gomze.co.za"; // Updated email address
if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");

// CSRF Protection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}

$error = false;
$error_message = '';
$fields = array('name', 'email', 'message', 'phone', 'subject');

// Validate all required fields
foreach ($fields as $field) {
    if (empty($_POST[$field]) || trim($_POST[$field]) == '') {
        $error = true;
        $error_message = 'All fields are required.';
    }
}

if (!$error) {
    // Sanitize and validate inputs
    $name = filter_var(stripslashes($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = filter_var(stripslashes($_POST['message']), FILTER_SANITIZE_STRING);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
        exit;
    }

    $e_subject = 'New Contact Form Submission from ' . $name;

    // Build email content
    $e_body = "You have been contacted by: " . $name . PHP_EOL . PHP_EOL;
    $e_reply = "E-mail: " . $email . PHP_EOL;
    $e_phone = "Phone: " . $phone . PHP_EOL;
    $e_subject_line = "Subject: " . $subject . PHP_EOL . PHP_EOL;
    $e_content = "Message:" . PHP_EOL . $message . PHP_EOL;

    $msg = wordwrap($e_body . $e_reply . $e_phone . $e_subject_line . $e_content, 70);

    // Set proper headers
    $headers = array();
    $headers[] = "From: " . $name . " <" . $email . ">";
    $headers[] = "Reply-To: " . $email;
    $headers[] = "Return-Path: " . $email;
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/plain; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: quoted-printable";
    $headers[] = "X-Mailer: PHP/" . phpversion();

    // Try to send email
    try {
        if (mail($address, $e_subject, $msg, implode(PHP_EOL, $headers))) {
            echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not send mail! Please check your PHP mail configuration.']);
        }
    } catch (Exception $e) {
        error_log("Mail sending failed: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while sending the message']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
?>
