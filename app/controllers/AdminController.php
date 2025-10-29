<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../model/Producto.php';

/**
 * Controlador del panel principal de administracion.
 */
class AdminController extends Controller
{
    /**
     * Muestra la vista principal del panel administrativo con resumenes, alertas y listados.
     *
     * @return void
     */
    public function principal(): void
    {
        $productos = Producto::all(500, 0);
        $normalizados = array_map([$this, 'normalizeProducto'], $productos);

        $lowStockList = $this->filterLowStock($normalizados);
        $expiringList = $this->filterExpiring($normalizados);

        $metrics = $this->buildMetrics($normalizados, count($lowStockList), count($expiringList));
        $statusSummary = $this->buildStatusSummary($normalizados);
        $lowStockProducts = array_map([$this, 'prepareProductForView'], array_slice($lowStockList, 0, 5));
        $expiringProducts = array_map([$this, 'prepareProductForView'], array_slice($expiringList, 0, 5));
        $recentActivity = $this->buildRecentActivity($normalizados);
        $alerts = $this->buildAlerts($lowStockList, $expiringList);

        $this->render(
            'admin.admin_principal',
            compact(
                'metrics',
                'statusSummary',
                'lowStockProducts',
                'expiringProducts',
                'recentActivity',
                'alerts'
            )
        );
    }

    /**
     * Muestra la configuración de notificaciones del panel.
     *
     * @return void
     */
    

    /**
     * Normaliza los campos de un producto para facilitar su consumo.
     *
     * @param array<string,mixed> $producto Datos originales del modelo.
     * @return array<string,mixed> Datos limpios y consistentes.
     */
    private function normalizeProducto(array $producto): array
    {
        $id = (string)($producto['id_producto'] ?? '');
        $nombre = trim((string)($producto['nom_producto'] ?? ''));
        $categoria = trim((string)($producto['categoria'] ?? ''));
        $unidad = trim((string)($producto['unidad'] ?? ''));
        $estadoRaw = trim((string)($producto['estado'] ?? ''));
        $estado = $estadoRaw === '' ? 'SIN_ESTADO' : strtoupper($estadoRaw);
        $cantidad = null;

        if (isset($producto['cantidad']) && $producto['cantidad'] !== '') {
            $cantidadValor = $producto['cantidad'];
            if (is_numeric($cantidadValor)) {
                $cantidad = (int)$cantidadValor;
            }
        }

        $fechaRaw = trim((string)($producto['fecha_vencimiento'] ?? ''));
        $fecha = $this->parseDate($fechaRaw);

        return [
            'id' => $id,
            'nombre' => $nombre,
            'categoria' => $categoria,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'estado' => $estado,
            'estado_label' => $estadoRaw !== '' ? $estadoRaw : 'Sin estado',
            'comentarios' => trim((string)($producto['comentarios'] ?? '')),
            'fecha_vencimiento' => $fecha,
            'fecha_vencimiento_raw' => $fechaRaw,
            'fecha_vencimiento_label' => $fecha instanceof \DateTimeImmutable ? $fecha->format('d/m/Y') : 'Sin fecha',
            'fecha_vencimiento_sort' => $fecha instanceof \DateTimeImmutable ? $fecha->format('Y-m-d') : '',
        ];
    }

    /**
     * Ajusta los datos de un producto para ser renderizados en la vista.
     *
     * @param array<string,mixed> $producto Producto normalizado.
     * @return array<string,mixed> Producto con etiquetas listas para la UI.
     */
    private function prepareProductForView(array $producto): array
    {
        $id = $producto['id'];

        return [
            'id' => $id,
            'nombre' => $producto['nombre'] !== '' ? $producto['nombre'] : 'Sin nombre',
            'categoria' => $producto['categoria'] !== '' ? $producto['categoria'] : 'Sin categoria',
            'unidad' => $producto['unidad'] !== '' ? $producto['unidad'] : '--',
            'cantidad' => $producto['cantidad'],
            'cantidad_label' => $producto['cantidad'] !== null ? (string)$producto['cantidad'] : 'N/D',
            'estado' => $producto['estado_label'],
            'estado_css' => $this->resolveStatusBadgeClass($producto['estado']),
            'status_key' => $this->resolveStatusKey($producto['estado']),
            'fecha_vencimiento' => $producto['fecha_vencimiento_label'],
            'fecha_vencimiento_sort' => $producto['fecha_vencimiento_sort'],
            'comentarios' => $producto['comentarios'],
            'url_show' => 'index.php?controller=Producto&action=show&id=' . urlencode($id),
            'url_edit' => 'index.php?controller=Producto&action=edit&id=' . urlencode($id),
        ];
    }

    /**
     * Construye los indicadores principales del tablero.
     *
     * @param array<int,array<string,mixed>> $productos Coleccion normalizada.
     * @param int $lowStockCount Total de productos con stock bajo.
     * @param int $expiringCount Total de productos por vencer.
     * @return array<int,array<string,mixed>> Lista de metricas a presentar.
     */
    private function buildMetrics(array $productos, int $lowStockCount, int $expiringCount): array
    {
        $total = count($productos);
        $available = 0;
        $agotados = 0;
        $revision = 0;
        $expired = 0;
        $today = new \DateTimeImmutable('today');

        foreach ($productos as $producto) {
            $estado = $producto['estado'] ?? '';
            $statusKey = $this->resolveStatusKey($estado);

            if ($statusKey === 'disponible') {
                $available++;
            } elseif ($statusKey === 'agotado') {
                $agotados++;
            } elseif ($statusKey === 'revision') {
                $revision++;
            }

            $fecha = $producto['fecha_vencimiento'] ?? null;
            if ($fecha instanceof \DateTimeImmutable && $fecha < $today) {
                $expired++;
            }
        }

        return [
            [
                'key' => 'total',
                'label' => 'Productos registrados',
                'value' => $total,
                'description' => 'Creados en el sistema',
                'tone' => 'neutral',
            ],
            [
                'key' => 'available',
                'label' => 'Disponibles',
                'value' => $available,
                'description' => 'Listos para entregar',
                'tone' => 'positive',
            ],
            [
                'key' => 'low',
                'label' => 'Stock bajo',
                'value' => $lowStockCount,
                'description' => 'Cinco unidades o menos',
                'tone' => 'warning',
            ],
            [
                'key' => 'expiring',
                'label' => 'Por vencer',
                'value' => $expiringCount,
                'description' => 'Siete dias proximos',
                'tone' => 'alert',
            ],
            [
                'key' => 'expired',
                'label' => 'Vencidos',
                'value' => $expired,
                'description' => 'Revisar y descartar',
                'tone' => 'danger',
            ],
        ];
    }

    /**
     * Calcula un resumen por estado para graficos y barras de progreso.
     *
     * @param array<int,array<string,mixed>> $productos Coleccion normalizada.
     * @return array<int,array<string,mixed>> Resumen por estado.
     */
    private function buildStatusSummary(array $productos): array
    {
        $totals = [
            'disponible' => 0,
            'agotado' => 0,
            'revision' => 0,
            'otros' => 0,
        ];

        foreach ($productos as $producto) {
            $statusKey = $this->resolveStatusKey($producto['estado'] ?? '');
            if (!array_key_exists($statusKey, $totals)) {
                $totals['otros']++;
                continue;
            }

            $totals[$statusKey]++;
        }

        $totalProductos = array_sum($totals);
        $totalProductos = $totalProductos > 0 ? $totalProductos : 1;

        $labels = [
            'disponible' => ['label' => 'Disponibles', 'tone' => 'positive'],
            'agotado' => ['label' => 'Agotados', 'tone' => 'warning'],
            'revision' => ['label' => 'En revision', 'tone' => 'alert'],
            'otros' => ['label' => 'Otros estados', 'tone' => 'neutral'],
        ];

        $summary = [];
        foreach ($totals as $key => $count) {
            $percentage = (int)round(($count / $totalProductos) * 100);

            $summary[] = [
                'key' => $key,
                'label' => $labels[$key]['label'],
                'count' => $count,
                'percentage' => $percentage,
                'tone' => $labels[$key]['tone'],
            ];
        }

        return $summary;
    }

    /**
     * Detecta productos con stock igual o menor a cinco unidades.
     *
     * @param array<int,array<string,mixed>> $productos Coleccion normalizada.
     * @return array<int,array<string,mixed>> Productos con stock bajo.
     */
    
    /**
     * Construye una linea de tiempo con los ultimos productos registrados.
     *
     * @param array<int,array<string,mixed>> $productos Coleccion normalizada.
     * @return array<int,array<string,mixed>> Eventos recientes.
     */
    private function buildRecentActivity(array $productos): array
    {
        usort(
            $productos,
            static function (array $a, array $b): int {
                $idA = (int)($a['id'] ?? 0);
                $idB = (int)($b['id'] ?? 0);
                return $idB <=> $idA;
            }
        );

        $slice = array_slice($productos, 0, 6);

        $timeline = [];
        foreach ($slice as $producto) {
            $timeline[] = [
                'title' => $producto['nombre'] !== '' ? $producto['nombre'] : 'Producto sin nombre',
                'categoria' => $producto['categoria'] !== '' ? $producto['categoria'] : 'Sin categoria',
                'estado' => $producto['estado_label'],
                'estado_css' => $this->resolveStatusBadgeClass($producto['estado']),
                'status_key' => $this->resolveStatusKey($producto['estado']),
                'meta' => $producto['fecha_vencimiento_label'],
                'url' => 'index.php?controller=Producto&action=show&id=' . urlencode($producto['id']),
            ];
        }

        return $timeline;
    }

    /**
     * Construye mensajes de alerta para la seccion de avisos rapidos.
     *
     * @param array<int,array<string,mixed>> $lowStock Productos con stock bajo.
     * @param array<int,array<string,mixed>> $expiring Productos proximos a vencer.
     * @return array<int,array<string,mixed>> Alertas combinadas.
     */
    private function buildAlerts(array $lowStock, array $expiring): array
    {
        $alerts = [];

        foreach (array_slice($lowStock, 0, 3) as $producto) {
            $alerts[] = [
                'type' => 'stock',
                'level' => 'danger',
                'message' => sprintf(
                    '%s solo tiene %s unidades disponibles.',
                    $producto['nombre'] !== '' ? $producto['nombre'] : 'Producto sin nombre',
                    $producto['cantidad'] !== null ? (string)$producto['cantidad'] : 'sin dato'
                ),
                'url' => 'index.php?controller=Producto&action=edit&id=' . urlencode($producto['id']),
            ];
        }

        foreach (array_slice($expiring, 0, 3) as $producto) {
            $alerts[] = [
                'type' => 'vencimiento',
                'level' => 'alert',
                'message' => sprintf(
                    '%s vence el %s.',
                    $producto['nombre'] !== '' ? $producto['nombre'] : 'Producto sin nombre',
                    $producto['fecha_vencimiento_label']
                ),
                'url' => 'index.php?controller=Producto&action=show&id=' . urlencode($producto['id']),
            ];
        }

        return $alerts;
    }

    /**
     * Convierte una cadena de fecha flexible en un objeto DateTimeImmutable.
     *
     * @param string $value Cadena de fecha a evaluar.
     * @return \DateTimeImmutable|null Fecha valida o null si no se puede interpretar.
     */
    private function parseDate(string $value): ?\DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value);
            if ($date instanceof \DateTimeImmutable) {
                return $date;
            }
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Devuelve la clave semantica asociada a un estado de producto.
     *
     * @param string $estado Estado normalizado o crudo.
     * @return string Clave consistente (disponible, agotado, revision u otros).
     */
    private function resolveStatusKey(string $estado): string
    {
        switch ($estado) {
            case 'DISPONIBLE':
            case 'ACTIVO':
            case 'AVAILABLE':
                return 'disponible';
            case 'AGOTADO':
            case 'SIN_STOCK':
                return 'agotado';
            case 'EN REVISION':
            case 'REVISION':
            case 'REVISION_PENDIENTE':
                return 'revision';
            case 'SIN_ESTADO':
            default:
                return 'otros';
        }
    }

    /**
     * Determina la clase CSS adecuada para mostrar el estado de un producto.
     *
     * @param string $estado Estado normalizado del producto.
     * @return string Clase CSS que representa el estado.
     */
    private function resolveStatusBadgeClass(string $estado): string
    {
        switch ($this->resolveStatusKey($estado)) {
            case 'disponible':
                return 'bg-emerald-100 text-emerald-700';
            case 'agotado':
                return 'bg-amber-100 text-amber-700';
            case 'revision':
                return 'bg-sky-100 text-sky-700';
            case 'otros':
            default:
                return 'bg-slate-100 text-slate-700';
        }
    }
}

