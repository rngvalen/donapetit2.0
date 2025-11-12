<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * EmailService
 * Servicio para envío de correos electrónicos usando PHPMailer
 */
class EmailService
{
    private array $config;
    private PHPMailer $mailer;

    public function __construct()
    {
        // Cargar configuración de email
        $configPath = __DIR__ . '/../../config/email.php';
        if (!file_exists($configPath)) {
            throw new Exception('Archivo de configuración de email no encontrado. Copia config/email.example.php a config/email.php');
        }

        $this->config = require $configPath;
        $this->mailer = new PHPMailer(true);
        $this->configurarSMTP();
    }

    /**
     * Configurar PHPMailer con SMTP
     */
    private function configurarSMTP(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['smtp']['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['smtp']['username'];
        $this->mailer->Password = $this->config['smtp']['password'];
        $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
        $this->mailer->Port = $this->config['smtp']['port'];
        $this->mailer->CharSet = 'UTF-8';

        // Configurar remitente
        $this->mailer->setFrom(
            $this->config['from_email'],
            $this->config['from_name']
        );

        // Configurar reply-to si está disponible
        if (!empty($this->config['reply_to'])) {
            $this->mailer->addReplyTo($this->config['reply_to']);
        }
    }

    /**
     * Enviar código de recuperación de contraseña
     *
     * @param string $email Email del destinatario
     * @param string $nombre Nombre del destinatario
     * @param string $codigo Código de 6 dígitos
     * @return bool true si se envió correctamente, false en caso contrario
     */
    public function enviarCodigoRecuperacion(string $email, string $nombre, string $codigo): bool
    {
        try {
            // Limpiar destinatarios anteriores
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Agregar destinatario
            $this->mailer->addAddress($email, $nombre);

            // Asunto
            $this->mailer->Subject = 'Código de recuperación de contraseña - DonAppetit';

            // Cuerpo HTML
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->buildResetTemplate($nombre, $codigo);

            // Texto alternativo (para clientes que no soportan HTML)
            $this->mailer->AltBody = $this->buildResetTextAlternative($nombre, $codigo);

            // Enviar
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Construir plantilla HTML para email de recuperación
     *
     * @param string $nombre Nombre del usuario
     * @param string $codigo Código de 6 dígitos
     * @return string HTML del email
     */
    private function buildResetTemplate(string $nombre, string $codigo): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de recuperación</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4CAF50; padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px;">DonAppetit</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 24px;">Hola, {$nombre}</h2>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.5;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta.
                            </p>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.5;">
                                Tu código de recuperación es:
                            </p>

                            <!-- Código -->
                            <div style="background-color: #f8f8f8; border: 2px dashed #4CAF50; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
                                <span style="font-size: 36px; font-weight: bold; color: #4CAF50; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                                    {$codigo}
                                </span>
                            </div>

                            <p style="margin: 0 0 10px 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                <strong>Este código expira en 15 minutos.</strong>
                            </p>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                Si no solicitaste este cambio, puedes ignorar este email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f8f8; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                Este es un email automático, por favor no respondas a este mensaje.
                            </p>
                            <p style="margin: 10px 0 0 0; color: #999999; font-size: 12px;">
                                &copy; 2025 DonAppetit. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Construir texto alternativo para email de recuperación
     *
     * @param string $nombre Nombre del usuario
     * @param string $codigo Código de 6 dígitos
     * @return string Texto plano del email
     */
    private function buildResetTextAlternative(string $nombre, string $codigo): string
    {
        return <<<TEXT
Hola, {$nombre}

Recibimos una solicitud para restablecer la contraseña de tu cuenta en DonAppetit.

Tu código de recuperación es: {$codigo}

Este código expira en 15 minutos.

Si no solicitaste este cambio, puedes ignorar este email.

---
Este es un email automático, por favor no respondas a este mensaje.
© 2025 DonAppetit. Todos los derechos reservados.
TEXT;
    }
}
