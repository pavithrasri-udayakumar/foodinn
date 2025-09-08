<?php
// forms/book-demo-call.php — returns JSON
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

require __DIR__ . '/../phpmailer/src/Exception.php';
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';

// ==== SETTINGS ====
$brandName = 'FoodInn';
$toEmail   = 'pavithrasri.u01@gmail.com';       // receive here
$gmailUser = 'pavithrasri.u01@gmail.com';       // Gmail account
$gmailPass = 'sepefurlrpsntlsb';                // 16-char App Password (no spaces)

// helper
function s($k){ return isset($_POST[$k]) ? trim((string)$_POST[$k]) : ''; }

// allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['ok'=>false, 'error'=>'Invalid request']);
  exit;
}

// honeypot
if (!empty($_POST['company'])) {
  echo json_encode(['ok'=>true]); // pretend OK to bots
  exit;
}

// validate
$name    = s('name');
$phone   = s('phone');
$email   = s('email');
$message = s('message');

if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['ok'=>false, 'error'=>'Enter a valid name, phone and email.']);
  exit;
}

// compose
$subject = "New Demo (Call) — {$brandName}";
$timeStr = date('Y-m-d H:i:s');

$html = "
  <h2>New Demo (Call) — {$brandName}</h2>
  <p><strong>Submitted:</strong> {$timeStr}</p>
  <table cellpadding='8' cellspacing='0' border='0' style='border:1px solid #eee'>
    <tr><td><strong>Name</strong></td><td>".htmlspecialchars($name)."</td></tr>
    <tr><td><strong>Phone</strong></td><td>".htmlspecialchars($phone)."</td></tr>
    <tr><td><strong>Email</strong></td><td>".htmlspecialchars($email)."</td></tr>
    <tr><td><strong>Message</strong></td><td>".nl2br(htmlspecialchars($message))."</td></tr>
  </table>
";
$text = "New Demo (Call) — {$brandName}\n\nSubmitted: {$timeStr}\n"
      . "Name: {$name}\nPhone: {$phone}\nEmail: {$email}\nMessage:\n{$message}\n";

// send
try {
  $mail = new PHPMailer(true);
  $mail->CharSet = 'UTF-8';
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = $gmailUser;
  $mail->Password   = $gmailPass;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  // DEV bypass for Windows/XAMPP TLS (remove once php.ini has cacert.pem)
  $mail->SMTPOptions = ['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true]];

  $mail->setFrom($gmailUser, "{$brandName} Demo Form");
  $mail->addAddress($toEmail, 'Demo Requests');
  $mail->addReplyTo($email, $name);

  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body    = $html;
  $mail->AltBody = $text;

  $mail->send();
  echo json_encode(['ok'=>true]);
} catch (Exception $e) {
  // Optional: log $e->getMessage() to a file
  echo json_encode(['ok'=>false, 'error'=>'Could not send right now. Please try again.']);
}
