<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phpmailer/src/Exception.php';
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';

header('Content-Type: text/plain'); // important for validate.js

$brandName = 'FoodInn';
$toEmail   = 'pavithrasri.u01@gmail.com';
$gmailUser = 'pavithrasri.u01@gmail.com';
$gmailPass = 'sepefurlrpsntlsb'; // app password (no spaces)

function s($k){ return isset($_POST[$k]) ? trim((string)$_POST[$k]) : ''; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo 'Invalid request'; exit; }
if (!empty($_POST['company'])) { echo 'OK'; exit; } // honeypot

$name = s('name'); $phone = s('phone'); $email = s('email'); $message = s('message');
if ($name==='' || $phone==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo 'Please enter a valid name, phone, and email.'; exit;
}

$subject = "New Demo Booking — {$brandName}";
$timeStr = date('Y-m-d H:i:s');

$html = "
  <h2>New Demo Booking — {$brandName}</h2>
  <p><strong>Submitted:</strong> {$timeStr}</p>
  <p><b>Name:</b> ".htmlspecialchars($name)."</p>
  <p><b>Phone:</b> ".htmlspecialchars($phone)."</p>
  <p><b>Email:</b> ".htmlspecialchars($email)."</p>
  <p><b>Message:</b> ".nl2br(htmlspecialchars($message))."</p>
";
$text = "New Demo Booking — {$brandName}\n\nSubmitted: {$timeStr}\nName: $name\nPhone: $phone\nEmail: $email\nMessage:\n$message\n";

try {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = $gmailUser;
  $mail->Password   = $gmailPass;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;
  // dev-only TLS bypass on XAMPP; remove once php.ini cacert.pem is set
  $mail->SMTPOptions = ['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true]];

  $mail->setFrom($gmailUser, "{$brandName} Demo Form");
  $mail->addAddress($toEmail, 'Demo Requests');
  $mail->addReplyTo($email, $name);
  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body    = $html;
  $mail->AltBody = $text;

  $mail->send();
  echo 'OK'; // <-- validate.js will show success and our hook will popup
} catch (Exception $e) {
  echo 'Mailer Error: ' . $e->getMessage(); // validate.js will show this as error + popup
}
