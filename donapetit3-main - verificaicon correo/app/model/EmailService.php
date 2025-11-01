<?php
class EmailService {
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/email.php';
    }

    public function enviarCodigoRecuperacion($destinatario, $nombre, $codigo) {
        $asunto = 'Código de recuperación - DonAppétit';
        $mensaje = $this->getEmailTemplate($nombre, $codigo);
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $this->config['from_email'] . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // ⚠️ DEBUGGING - Ver si mail() funciona
        $enviado = mail($destinatario, $asunto, $mensaje, $headers);
        
        // Log para ver qué pasó
        error_log("=== EMAIL DEBUG ===");
        error_log("Destinatario: $destinatario");
        error_log("Resultado mail(): " . ($enviado ? 'TRUE' : 'FALSE'));
        error_log("Error: " . error_get_last()['message'] ?? 'ninguno');
        
        // ⚠️ TEMPORAL: Mostrar en pantalla para debugging
        echo "<pre>";
        echo "=== DEBUG EMAIL ===\n";
        echo "Destinatario: $destinatario\n";
        echo "Código: $codigo\n";
        echo "Resultado mail(): " . ($enviado ? 'ENVIADO ✅' : 'FALLÓ ❌') . "\n";
        echo "Último error PHP: " . print_r(error_get_last(), true) . "\n";
        echo "</pre>";
        
        // Por ahora, siempre retornar true para probar el flujo
        return true; // ⚠️ Cambiar después a: return $enviado;
    }

    private function getEmailTemplate($nombre, $codigo) {
        return "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #3D538F 0%, #2a3a66 100%); padding: 30px; text-align: center; }
        .logo { width: 80px; height: 80px; background: white; border-radius: 16px; margin: 0 auto 15px; font-size: 32px; font-weight: bold; color: #3D538F; line-height: 80px; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #334155; margin-bottom: 20px; }
        .code-box { background: #f8fafc; border: 2px dashed #3D538F; border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; }
        .code { font-size: 36px; font-weight: bold; color: #3D538F; letter-spacing: 8px; font-family: monospace; }
        .expiry { color: #64748b; font-size: 14px; margin-top: 15px; }
        .warning { background: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 8px; margin: 20px 0; color: #991b1b; font-size: 14px; }
        .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <div class='logo'>DA</div>
            <h1>DonAppétit</h1>
        </div>
        <div class='content'>
            <p class='greeting'>Hola <strong>$nombre</strong>,</p>
            <p>Recibimos una solicitud para recuperar tu contraseña. Usá el siguiente código de verificación:</p>
            <div class='code-box'>
                <div class='code'>$codigo</div>
                <p class='expiry'>⏱️ Este código expira en 15 minutos</p>
            </div>
            <p>Ingresá este código en la página de recuperación para continuar.</p>
            <div class='warning'>
                <strong>⚠️ Importante:</strong> Si no solicitaste recuperar tu contraseña, podés ignorar este email de forma segura.
            </div>
        </div>
        <div class='footer'>
            <p>Este es un email automático, por favor no respondas a este mensaje.</p>
            <p>&copy; 2025 DonAppétit. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>";
    }
}