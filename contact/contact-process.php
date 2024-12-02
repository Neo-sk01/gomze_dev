<?php
$address = "info@gomze.co.za"; // Updated email address
if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");

$error = false;
$fields = array('name', 'email', 'message', 'phone', 'subject');

foreach ($fields as $field) {
    if (empty($_POST[$field]) || trim($_POST[$field]) == '') {
        $error = true;
    }
}

if (!$error) {

    $name = stripslashes($_POST['name']);
    $email = trim($_POST['email']);
    $message = stripslashes($_POST['message']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);

    $e_subject = 'You\'ve been contacted by ' . $name . '.';

    // Configuration option.
    // Developers, add more fields here if necessary.

    $e_body = "You have been contacted by: $name" . PHP_EOL . PHP_EOL;
    $e_reply = "E-mail: $email" . PHP_EOL . PHP_EOL;
    $e_content = "Message:\r\n$message \r\nPhone: $phone \r\nSubject: $subject" . PHP_EOL;

    $msg = wordwrap($e_body . $e_reply . $e_content, 70); // Include $e_content in $msg

    $headers = "From: $email" . PHP_EOL;
    $headers .= "Reply-To: $email" . PHP_EOL;
    $headers .= "Content-type: text/plain; charset=utf-8" . PHP_EOL;
    $headers .= "Content-Transfer-Encoding: quoted-printable" . PHP_EOL;

    if (mail($address, $e_subject, $msg, $headers)) { // Correct arguments
        // Email sent successfully
        echo 'Success';
    } else {
        echo 'ERROR!';
    }
} else {
    echo 'ERROR: Missing fields!';
}
?>
