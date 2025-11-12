<?php
/**
 * Script de prueba para verificar el envío de emails
 *
 * IMPORTANTE: Configura primero config/email.php con tus credenciales SMTP reales
 * Luego ejecuta: php test_email.php
 */

require_once __DIR__ . '/app/services/EmailService.php';

echo "=== Test de Email Service ===\n\n";

// Email de destino para la prueba (cámbialo por tu email)
$emailDestino = 'tu-email@example.com';
$nombreDestino = 'Usuario de Prueba';
$codigoPrueba = '123456';

echo "Configuración:\n";
echo "- Destinatario: $emailDestino\n";
echo "- Código de prueba: $codigoPrueba\n\n";

try {
    echo "Inicializando EmailService...\n";
    $emailService = new EmailService();

    echo "Enviando email de recuperación...\n";
    $resultado = $emailService->enviarCodigoRecuperacion(
        $emailDestino,
        $nombreDestino,
        $codigoPrueba
    );

    if ($resultado) {
        echo "\n✓ Email enviado exitosamente!\n";
        echo "Revisa tu bandeja de entrada (y spam) en: $emailDestino\n";
    } else {
        echo "\n✗ Error: No se pudo enviar el email.\n";
        echo "Revisa:\n";
        echo "1. Que config/email.php existe y tiene credenciales correctas\n";
        echo "2. Los logs de error de PHP\n";
        echo "3. Que las credenciales SMTP sean válidas\n";
    }

} catch (Exception $e) {
    echo "\n✗ Excepción capturada: " . $e->getMessage() . "\n";
    echo "\nPosibles soluciones:\n";
    echo "1. Verifica que config/email.php existe (copia desde config/email.example.php)\n";
    echo "2. Configura tus credenciales SMTP correctas\n";
    echo "3. Si usas Gmail, necesitas un 'App Password'\n";
    echo "4. Para pruebas, considera usar Mailtrap.io\n";
}

echo "\n=== Fin del test ===\n";
