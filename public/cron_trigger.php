<?php
/**
 * Trigger manual del CRON para liberar stock expirado
 *
 * Este archivo permite ejecutar la liberación de stock desde el navegador
 * útil cuando no se puede configurar un CRON real.
 *
 * IMPORTANTE: En producción, proteger este archivo con autenticación
 * o eliminarlo y usar un CRON real del sistema.
 *
 * Uso: Acceder a http://tudominio.com/cron_trigger.php?key=tu_clave_secreta
 */

declare(strict_types=1);

// Clave secreta para proteger el endpoint (cambiar en producción)
define('CRON_SECRET_KEY', 'donappetit2025');

// Verificar clave de seguridad
if (!isset($_GET['key']) || $_GET['key'] !== CRON_SECRET_KEY) {
    http_response_code(403);
    die('Acceso denegado');
}

require_once __DIR__ . '/../app/model/Solicitud.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== Liberación de Stock Expirado ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $solicitudModel = new Solicitud();
    $liberadas = $solicitudModel->liberarStockExpirado();

    echo "✓ Proceso completado\n";
    echo "Reservas liberadas: {$liberadas}\n";

    if ($liberadas > 0) {
        echo "\nSe devolvió el stock de {$liberadas} solicitudes expiradas.\n";
    } else {
        echo "\nNo hay reservas expiradas en este momento.\n";
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo "✗ Error al ejecutar el proceso:\n";
    echo $e->getMessage() . "\n";
}

echo "\n=== Fin del proceso ===\n";
