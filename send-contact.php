
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

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

    /* ================= ADMIN MAIL ================= */
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $env['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $env['MAIL_USERNAME'];
    $mail->Password   = $env['MAIL_PASSWORD'];
    $mail->SMTPSecure = $env['MAIL_ENCRYPTION'];
    $mail->Port       = $env['MAIL_PORT'];

    $mail->setFrom($env['MAIL_FROM_ADDRESS'], $env['MAIL_FROM_NAME']);
    $mail->addAddress($env['ADMIN_EMAIL']);

    $mail->isHTML(true);
    $mail->Subject = 'New Portfolio Contact Message';
    $mail->Body = "
        <h3>New Contact Enquiry</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Subject:</b> {$subject}</p>
        <p><b>Message:</b><br>{$message}</p>
    ";

    $mail->send();

    /* ================= USER AUTO-REPLY ================= */
    $reply = new PHPMailer(true);
    $reply->isSMTP();
    $reply->Host       = $env['MAIL_HOST'];
    $reply->SMTPAuth   = true;
    $reply->Username   = $env['MAIL_USERNAME'];
    $reply->Password   = $env['MAIL_PASSWORD'];
    $reply->SMTPSecure = $env['MAIL_ENCRYPTION'];
    $reply->Port       = $env['MAIL_PORT'];

    $reply->setFrom($env['MAIL_FROM_ADDRESS'], $env['MAIL_FROM_NAME']);
    $reply->addAddress($email, $name);

    $reply->isHTML(true);
    $reply->Subject = 'Thanks for contacting me';
    $reply->Body = "
        <p>Hi {$name},</p>
        <p>Thank you for contacting me. I have received your message and will reply soon.</p>
        <br>
        <p>Regards,<br>{$env['MAIL_FROM_NAME']}</p>
    ";

    $reply->send();

    echo 'OK';

} catch (Exception $e) {
    echo 'Mail Error: ' . $e->getMessage();
}
