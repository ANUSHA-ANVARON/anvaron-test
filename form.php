<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $company = htmlspecialchars($_POST['company']);
    $message = htmlspecialchars($_POST['message']);

    // Email configuration
    $to = "sathishk2212@gmail.com";
    $subject = "New Contact Form Submission";
    $body = "Name: $name\nEmail: $email\nPhone: $phone\nCompany: $company\nMessage: $message";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        // Redirect with success message
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=success");
        exit;
    } else {
        // Redirect with failure message
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=fail");
        exit;
    }
} else {
    // If accessed without POST, show 405 error
    header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed");
    echo "405 - HTTP verb used to access this page is not allowed.";
    exit;
}
?>
