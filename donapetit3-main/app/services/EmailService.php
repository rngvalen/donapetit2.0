<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    private PHPMailer $mailer;
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/email.php';
        $this->mailer = new PHPMailer(true);
        $this->configurarSMTP();
    }

    private function configurarSMTP(): void
    {
        $smtp = $this->config['smtp'];

        $this->mailer->isSMTP();
        $this->mailer->Host = $smtp['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $smtp['username'];
        $this->mailer->Password = $smtp['password'];

        // Manejar encryption como string o constante
        if (is_string($smtp['encryption'])) {
            $this->mailer->SMTPSecure = strtolower($smtp['encryption']) === 'tls'
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $this->mailer->SMTPSecure = $smtp['encryption'];
        }

        $this->mailer->Port = $smtp['port'];
        $this->mailer->CharSet = 'UTF-8';

        // Configurar remitente
        $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);

        // Solo agregar reply_to si no está vacío
        if (!empty($this->config['reply_to'])) {
            $this->mailer->addReplyTo($this->config['reply_to'], $this->config['from_name']);
        }
    }

    /**
     * Envía un código de recuperación de contraseña por email
     */
    public function enviarCodigoRecuperacion(string $email, string $nombre, string $codigo): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Código de recuperación de contraseña - DonAppetit';
            $this->mailer->Body = $this->buildResetTemplate($nombre, $codigo);
            $this->mailer->AltBody = $this->buildResetTextAlternative($nombre, $codigo);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error al enviar email de recuperación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Construye el template HTML para el email de recuperación
     */
    private function buildResetTemplate(string $nombre, string $codigo): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f4f4f4;
            border-radius: 10px;
            padding: 30px;
        }
        .header {
            background-color: #416d56;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .code-box {
            background-color: white;
            border: 2px dashed #416d56;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #416d56;
            letter-spacing: 5px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DonAppetit</h1>
            <p>Recuperación de Contraseña</p>
        </div>

        <p>Hola <strong>{$nombre}</strong>,</p>

        <p>Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código de verificación para continuar:</p>

        <div class="code-box">
            <div class="code">{$codigo}</div>
            <p style="margin: 10px 0 0 0; color: #666;">Código de verificación</p>
        </div>

        <div class="warning">
            <strong>⏱️ Importante:</strong> Este código expirará en <strong>15 minutos</strong>.
        </div>

        <p>Si no solicitaste restablecer tu contraseña, puedes ignorar este correo de forma segura.</p>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; 2025 DonAppetit - Plataforma de donación de alimentos</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Construye el texto alternativo sin HTML
     */
    private function buildResetTextAlternative(string $nombre, string $codigo): string
    {
        return <<<TEXT
DonAppetit - Recuperación de Contraseña

Hola {$nombre},

Recibimos una solicitud para restablecer tu contraseña.

Tu código de verificación es: {$codigo}

IMPORTANTE: Este código expirará en 15 minutos.

Si no solicitaste restablecer tu contraseña, puedes ignorar este correo de forma segura.

---
Este es un correo automático, por favor no respondas a este mensaje.
© 2025 DonAppetit - Plataforma de donación de alimentos
TEXT;
    }
}
