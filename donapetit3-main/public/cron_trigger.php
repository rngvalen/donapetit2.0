<?php
/**
 * Trigger manual para ejecutar el CRON de liberacion de stock
 *
 * Uso: http://localhost/donapetit3-main/public/cron_trigger.php?key=donappetit2025
 *
 * IMPORTANTE: Cambiar CRON_SECRET_KEY en produccion
 */

declare(strict_types=1);

// Clave secreta para proteger el endpoint (CAMBIAR EN PRODUCCION)
define('CRON_SECRET_KEY', 'donappetit2025');

// Verificar clave de acceso
$key = $_GET['key'] ?? '';

if ($key !== CRON_SECRET_KEY) {
    http_response_code(403);
    die('Acceso denegado');
}

// Ejecutar el script CRON
$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/app/model/Solicitud.php';

try {
    $solicitudModel = new Solicitud();
    $liberadas = $solicitudModel->liberarStockExpirado();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'solicitudes_liberadas' => $liberadas,
        'mensaje' => $liberadas > 0
            ? "Se liberaron {$liberadas} solicitudes expiradas"
            : "No hay solicitudes expiradas para liberar"
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => $e->getMessage()
    ]);
    error_log("CRON trigger falló: " . $e->getMessage());
}
