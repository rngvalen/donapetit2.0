<?php
/**
 * Script CRON para liberar stock de solicitudes expiradas
 *
 * Ejecutar cada 5 minutos (Linux crontab):
 * (usar sintaxis cron standard sin el asterisco inicial)
 *
 * Windows Task Scheduler:
 * Programa: C:\xampp\php\php.exe
 * Argumentos: C:\xampp\htdocs\donapetit3-main\cron\liberar_stock_expirado.php
 */

declare(strict_types=1);

// Ajustar path relativo al directorio raiz del proyecto
$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/app/model/Solicitud.php';

try {
    $solicitudModel = new Solicitud();
    $liberadas = $solicitudModel->liberarStockExpirado();

    $timestamp = date('Y-m-d H:i:s');

    if ($liberadas > 0) {
        echo "[{$timestamp}] ✓ Se liberaron {$liberadas} solicitudes expiradas\n";
    } else {
        echo "[{$timestamp}] ℹ No hay solicitudes expiradas para liberar\n";
    }

    exit(0);

} catch (Throwable $e) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[{$timestamp}] ✗ ERROR: " . $e->getMessage() . "\n";
    error_log("CRON liberar_stock_expirado falló: " . $e->getMessage());
    exit(1);
}
