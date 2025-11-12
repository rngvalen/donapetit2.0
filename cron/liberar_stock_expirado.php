<?php
/**
 * Script CRON para liberar stock de solicitudes expiradas
 *
 * Este script debe ejecutarse cada 5-10 minutos para verificar y liberar
 * reservas de stock que han expirado (más de 2 horas sin confirmar retiro)
 *
 * Configuración en crontab (Linux):
 * Cada 5 minutos: php /ruta/completa/cron/liberar_stock_expirado.php >> /ruta/logs/cron.log 2>&1
 *
 * Configuración en Windows Task Scheduler:
 * - Acción: php.exe
 * - Argumentos: C:\xampp\htdocs\donapetit3-main\cron\liberar_stock_expirado.php
 * - Repetir cada: 5 minutos
 */

declare(strict_types=1);

// Evitar ejecución desde navegador
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Este script solo puede ejecutarse desde línea de comandos.');
}

require_once __DIR__ . '/../app/model/Solicitud.php';

echo "[" . date('Y-m-d H:i:s') . "] Iniciando verificación de reservas expiradas...\n";

try {
    $solicitudModel = new Solicitud();
    $liberadas = $solicitudModel->liberarStockExpirado();

    if ($liberadas > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] ✓ Se liberaron {$liberadas} reservas expiradas\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] ℹ No hay reservas expiradas para liberar\n";
    }

} catch (\Throwable $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Finalizado\n";
exit(0);
