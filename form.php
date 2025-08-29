<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust paths if you put PHPMailer elsewhere
require __DIR__ . '/assets/phpmailer/src/PHPMailer.php';
require __DIR__ . '/assets/phpmailer/src/SMTP.php';
require __DIR__ . '/assets/phpmailer/src/Exception.php';

// Basic sanitization helpers
function s($v) { return trim(filter_var($v ?? '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)); }
function valid_email($v) { return (bool)filter_var($v ?? '', FILTER_VALIDATE_EMAIL); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// Where to go back after submit (used by your pages' ?status logic)
$redirect = basename($_POST['redirect'] ?? 'contact-us.html');

// Form fields (adjust names if your inputs differ)
$name    = s($_POST['name']    ?? '');
$email   = $_POST['email']     ?? '';
$phone   = s($_POST['phone']   ?? '');
$company = s($_POST['company'] ?? '');
$message = trim($_POST['message'] ?? '');
$honeypot = $_POST['website'] ?? ''; // bots fill this

// If honeypot is filled, pretend success (silently drop)
if ($honeypot !== '') {
  header("Location: {$redirect}?status=success");
  exit;
}

// Simple required checks
if (!$name || !valid_email($email) || !$message) {
  header("Location: {$redirect}?status=fail");
  exit;
}

$mail = new PHPMailer(true);
try {
  // Gmail SMTP settings
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'harshavardhan.kuthadi@anvaron.in';
  $mail->Password   = 'aezakmi@6';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587; // TLS
  $mail->CharSet    = 'UTF-8';

  // From must be your Gmail when using Gmail SMTP
  $mail->setFrom('harshavardhan.kuthadi@anvaron.in', 'Anvaron Website');
  // Where you want to receive the leads:
  $mail->addAddress('harshavardhan.kuthadi@anvaron.in', 'Forms Inbox');
  // Let replies go to the sender
  $mail->addReplyTo($email, $name);

  $mail->Subject = 'New website enquiry';
  $mail->Body    =
    "Name: {$name}\n"
    ."Email: {$email}\n"
    ."Phone: {$phone}\n"
    ."Company: {$company}\n\n"
    ."Message:\n{$message}\n";

  $mail->send();
  header("Location: {$redirect}?status=success");
} catch (Exception $e) {
  // You can log $mail->ErrorInfo to a file if needed
  header("Location: {$redirect}?status=fail");
}
exit;
