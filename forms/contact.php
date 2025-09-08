<?php
// forms/contact.php — PHPMailer + Gmail/SMTP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// PHPMailer includes (manual install)


require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

// Collect fields
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? 'New message from website');
$message = trim($_POST['message'] ?? '');

// Basic validation
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
  http_response_code(400);
  exit('Please provide a valid name, email, and message.');
}

try {
  $mail = new PHPMailer(true);

  // SMTP (edit these 3 lines)
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';               // e.g. Gmail
  $mail->SMTPAuth   = true;
  $mail->Username   = 'pavithrasri.u01@gmail.com'; // EDIT ME: sender address
  $mail->Password   = 'sepefurlrpsntlsb';    // EDIT ME: Gmail App Password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or ENCRYPTION_SMTPS with 465
  $mail->Port       = 587;

  // $mail->SMTPDebug = 2; // <- uncomment while debugging only

  // From/To
  $mail->setFrom('pavithrasri.u01@gmail.com', 'FoodIn Website'); // same as Username
  $mail->addAddress('support@foodinn.ie', 'FoodIn Support');        // EDIT ME: recipient
  $mail->addReplyTo($email, $name); // reply to the visitor

  // Content
  $mail->Subject = "Website: {$subject}";
  $mail->Body    = "New message from About page:\n\n"
                 . "Name: {$name}\n"
                 . "Email: {$email}\n"
                 . "Subject: {$subject}\n\n"
                 . "Message:\n{$message}\n";
  $mail->AltBody = $mail->Body;

  $mail->send();
  echo 'OK'; // the JS in about.html expects this on success
} catch (Exception $e) {
  http_response_code(500);
  echo 'Mailer Error: ' . $mail->ErrorInfo;
}
