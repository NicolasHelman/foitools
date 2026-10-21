<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';
require __DIR__ . '/admin/class/Conexion.php';
require __DIR__ . '/admin/class/Config.php';

header('Content-Type: application/json; charset=utf-8');
session_start();

function out(bool $ok, string $msg, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'Método no permitido.', 405);
if (!empty($_POST['website'])) out(true, '¡Gracias!'); // honeypot anti-spam

if (time() - ($_SESSION['last_send'] ?? 0) < 30) out(false, 'Esperá unos segundos antes de enviar otra consulta.', 429);

$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim($_POST['email'] ?? '');
$phone   = trim(strip_tags($_POST['phone'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

if ($name === '' || mb_strlen($name) > 100) out(false, 'Ingresá tu nombre.', 422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) out(false, 'Ingresá un email válido.', 422);
if (mb_strlen($message) < 5 || mb_strlen($message) > 3000) out(false, 'Escribí tu consulta.', 422);

$cfg = Config::all();

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $cfg['smtp_host']   ?? '';
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['smtp_user']   ?? '';
    $mail->Password   = $cfg['smtp_pass']   ?? '';
    $mail->SMTPSecure = $cfg['smtp_secure'] ?? 'tls';
    $mail->Port       = (int)($cfg['smtp_port'] ?? 587);
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($cfg['smtp_from'] ?? '', $cfg['smtp_name'] ?? '');
    $mail->addAddress($cfg['email_to'] ?? '');
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'Nueva consulta web de ' . $name;
    $mail->isHTML(true);
    $h = fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    $mail->Body = '<h2>Nueva consulta desde la web</h2>'
        . '<p><b>Nombre:</b> ' . $h($name) . '</p>'
        . '<p><b>Email:</b> ' . $h($email) . '</p>'
        . '<p><b>Teléfono:</b> ' . $h($phone ?: '-') . '</p>'
        . '<p><b>Consulta:</b><br>' . nl2br($h($message)) . '</p>';
    $mail->AltBody = "Nombre: $name\nEmail: $email\nTeléfono: $phone\n\n$message";

    $mail->send();
    $_SESSION['last_send'] = time();
    out(true, '¡Consulta enviada! Te respondemos a la brevedad.');
} catch (Exception $e) {
    error_log('PHPMailer: ' . $mail->ErrorInfo);
    out(false, 'No pudimos enviar el mensaje. Probá por WhatsApp.', 500);
}
