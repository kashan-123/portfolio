<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';


// Load SMTP settings
$env = parse_ini_file(__DIR__ . '/.env');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request');
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    exit('All fields are required');
}

try {
    /* ================= ADMIN EMAIL ================= */
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $env['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $env['MAIL_USERNAME'];
    $mail->Password   = $env['MAIL_PASSWORD'];
    $mail->SMTPSecure = $env['MAIL_ENCRYPTION'];
    $mail->Port       = (int)$env['MAIL_PORT'];

    $mail->setFrom($env['MAIL_FROM_ADDRESS'], $env['MAIL_FROM_NAME']);
    $mail->addAddress($env['ADMIN_EMAIL']);

    $mail->isHTML(true);
    $mail->Subject = "New Portfolio Contact Message";
    $mail->Body = "
        <h2>New Contact Enquiry</h2>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Subject:</b> {$subject}</p>
        <p><b>Message:</b><br>" . nl2br($message) . "</p>
        <hr>
        <p>Portfolio Contact Form</p>
    ";

    $mail->send();

    /* ================= USER AUTO REPLY ================= */
    $reply = new PHPMailer(true);
    $reply->isSMTP();
    $reply->Host       = $env['MAIL_HOST'];
    $reply->SMTPAuth   = true;
    $reply->Username   = $env['MAIL_USERNAME'];
    $reply->Password   = $env['MAIL_PASSWORD'];
    $reply->SMTPSecure = $env['MAIL_ENCRYPTION'];
    $reply->Port       = (int)$env['MAIL_PORT'];

    $reply->setFrom($env['MAIL_FROM_ADDRESS'], $env['MAIL_FROM_NAME']);
    $reply->addAddress($email, $name);

    $reply->isHTML(true);
    $reply->Subject = "Thanks for contacting me";
    $reply->Body = "
    <p>Hi {$name},</p>

    <p>
        Thank you for reaching out. I’ve received your message and will respond as soon as possible.
    </p>

    <p>
        You can also check out my work here:
        <br>
        🔗 <a href='https://kashan-123.github.io/portfolio/' target='_blank'>
        Visit My Portfolio</a>
    </p>

    <br>

    <p>
        Best regards,<br>
        <strong>{$env['MAIL_FROM_NAME']}</strong>
    </p>
";


    $reply->send();

    echo "OK";
} catch (Exception $e) {
    echo "Mail Error: " . $e->getMessage();
}
