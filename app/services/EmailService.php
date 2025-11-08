<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';

class EmailService
{
    /** @var array<string,mixed> */
    private array $config;

    /**
     * @param array<string,mixed>|null $config Override completo para pruebas
     */
    public function __construct(?array $config = null)
    {
        $this->config = $config ?? require __DIR__ . '/../../config/email.php';
    }

    public function enviarCodigoRecuperacion(string $destinatario, string $nombre, string $codigo): bool
    {
        $mail = $this->createMailer();

        $fromEmail = (string)($this->config['from_email'] ?? 'noreply@example.com');
        $fromName  = (string)($this->config['from_name'] ?? 'DonAppetit');
        $replyTo   = (string)($this->config['reply_to'] ?? '');

        try {
            $mail->setFrom($fromEmail, $fromName);
            if ($replyTo !== '') {
                $mail->addReplyTo($replyTo, $fromName);
            }
            $mail->addAddress($destinatario, $nombre);

            $mail->Subject = 'Codigo de recuperacion - DonAppetit';
            $mail->Body    = $this->buildResetTemplate($nombre, $codigo);
            $mail->AltBody = "Recibimos una solicitud para recuperar tu contrasena.\n"
                . "Tu codigo de verificacion es: {$codigo}\n\n"
                . "Este codigo expira en 15 minutos.";

            return $mail->send();
        } catch (Exception $e) {
            error_log('Error al enviar email: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private function createMailer(): PHPMailer
    {
        $smtpConfig = $this->config['smtp'] ?? [];

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = (string)($smtpConfig['host'] ?? 'localhost');
        $mail->Port       = (int)($smtpConfig['port'] ?? 25);
        $mail->SMTPAuth   = true;
        $mail->Username   = (string)($smtpConfig['username'] ?? '');
        $mail->Password   = (string)($smtpConfig['password'] ?? '');

        $encryption = strtolower((string)($smtpConfig['encryption'] ?? ''));
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
        }

        return $mail;
    }

    private function buildResetTemplate(string $nombre, string $codigo): string
    {
        $nombreEscapado = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $codigoEscapado = htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #3D538F 0%, #2a3a66 100%); padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px 30px; color: #1e293b; }
        .greeting { font-size: 18px; margin-bottom: 20px; }
        .code-box { background: #f8fafc; border: 2px dashed #3D538F; border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; }
        .code { font-size: 36px; font-weight: bold; color: #3D538F; letter-spacing: 8px; font-family: monospace; }
        .expiry { color: #64748b; font-size: 14px; margin-top: 15px; }
        .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DonAppetit</h1>
        </div>
        <div class="content">
            <p class="greeting">Hola <strong>{$nombreEscapado}</strong>,</p>
            <p>Recibimos una solicitud para recuperar tu contrasena. Usa el siguiente codigo:</p>
            <div class="code-box">
                <div class="code">{$codigoEscapado}</div>
                <p class="expiry">Este codigo expira en 15 minutos.</p>
            </div>
            <p>Si no hiciste esta solicitud podes ignorar este mensaje.</p>
        </div>
        <div class="footer">
            <p>Este email se genero automaticamente, por favor no respondas.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
