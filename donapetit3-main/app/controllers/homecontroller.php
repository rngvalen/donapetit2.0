<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../model/donacion.php';
require_once __DIR__ . '/../../config/bdconexion.php';

/**
 * Controlador responsable de la pagina inicial del panel.
 */
class HomeController extends Controller
{
    /**
     * Muestra la vista de bienvenida del sistema según el rol.
     *
     * @return void
     */
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $userName = $_SESSION['user']['name'];
        $userRole = $_SESSION['user']['rol'] ?? 'donante';

        // Si es admin, redirigir al panel administrativo
        if ($userRole === 'admin') {
            header('Location: ?controller=Admin&action=principal');
            exit;
        }

        // Verificar si el usuario completó su perfil
        if (!$this->hasCompletedProfile($userRole)) {
            header('Location: ?controller=Profile&action=completarPerfil');
            exit;
        }

        // Redirigir según el rol
        if ($userRole === 'receptor') {
            $this->render('home.receptor', compact('userName'));
        } else {
            $this->render('home.index', compact('userName'));
        }
    }

    /**
     * Verifica si el usuario completó su perfil adicional.
     *
     * @param string $role
     * @return bool
     */
    private function hasCompletedProfile(string $role): bool
    {
        if (!isset($_SESSION['user']['id'])) {
            return false;
        }

        $userId = $_SESSION['user']['id'];
        $db = new Database();
        $conn = $db->getConnection();

        if ($role === 'donante') {
            $stmt = $conn->prepare("SELECT id_usu_donante FROM donante WHERE id_usu_donante = :id");
        } else {
            $stmt = $conn->prepare("SELECT id_usu_receptor FROM receptor WHERE id_usu_receptor = :id");
        }

        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Muestra la vista de estadisticas con informacion proveniente de la base de datos.
     * Accesible para donantes y receptores.
     *
     * @return void
     */
    public function statics(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $userRole = $_SESSION['user']['rol'] ?? 'donante';
        $userName = $_SESSION['user']['name'];
        $userId = (int)$_SESSION['user']['id'];

        // Obtener estadísticas según el rol
        if ($userRole === 'donante') {
            $stats = $this->buildStatisticsContext($userId);
        } else {
            $stats = $this->buildReceptorStatisticsContext($userId);
        }

        $this->render('statics.statics_main', array_merge($stats, compact('userName')));
    }

    /**
     * Obtiene los datos necesarios para el tablero de estadisticas.
     *
     * @param int $idDonante ID del donante para filtrar estadísticas
     * @return array<string,mixed>
     */
    private function buildStatisticsContext(int $idDonante): array
    {
        $donacionModel = new Donacion();

        $totalRegistroDonaciones = $donacionModel->totalRegistros($idDonante);
        $totalCantidadDonada = $donacionModel->totalCantidad($idDonante);
        $alimentosSalvados = $donacionModel->totalPorEstado('ENTREGADO', $idDonante);

        if ($alimentosSalvados === 0) {
            $alimentosSalvados = $totalCantidadDonada;
        }

        $topProductos = $donacionModel->topProductos(5, $idDonante);
        $frecuenciaMensual = $donacionModel->frecuenciaMensual(6, $idDonante);

        return [
            'totalDonaciones' => $totalRegistroDonaciones,
            'totalCantidadDonada' => $totalCantidadDonada,
            'alimentosSalvados' => $alimentosSalvados,
            'topProductos' => $topProductos,
            'frecuenciaMensual' => $frecuenciaMensual,
        ];
    }

    /**
     * Obtiene los datos de estadísticas para receptores.
     * Usa las tablas: solicitud, productos_solicitados, detalle, productos_donante, catalogo_productos
     *
     * @param int $idReceptor ID del receptor para filtrar estadísticas
     * @return array<string,mixed>
     */
    private function buildReceptorStatisticsContext(int $idReceptor): array
    {
        $db = new Database();
        $conn = $db->getConnection();

        // Total de solicitudes realizadas
        $stmtTotal = $conn->prepare("
            SELECT COUNT(*) as total
            FROM solicitud
            WHERE id_receptor = :id_receptor
        ");
        $stmtTotal->bindParam(':id_receptor', $idReceptor, PDO::PARAM_INT);
        $stmtTotal->execute();
        $totalSolicitudes = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de alimentos recibidos (desde detalle con donacion_efectiva = 1)
        $stmtRecibidos = $conn->prepare("
            SELECT COALESCE(SUM(d.cantidad_donada), 0) as total
            FROM detalle d
            INNER JOIN productos_solicitados ps ON d.id_producto_solicitado = ps.id_producto_solicitado
            INNER JOIN solicitud s ON ps.id_solicitud = s.id_solicitud
            WHERE s.id_receptor = :id_receptor
            AND d.donacion_efectiva = 1
        ");
        $stmtRecibidos->bindParam(':id_receptor', $idReceptor, PDO::PARAM_INT);
        $stmtRecibidos->execute();
        $alimentosRecibidos = (int)$stmtRecibidos->fetch(PDO::FETCH_ASSOC)['total'];

        // Top 5 productos más solicitados (desde productos_solicitados)
        $stmtTopProductos = $conn->prepare("
            SELECT
                cat.nom_producto,
                SUM(ps.cantidad_solicitada) as cantidad
            FROM productos_solicitados ps
            INNER JOIN solicitud s ON ps.id_solicitud = s.id_solicitud
            INNER JOIN productos_donante pd ON ps.id_producto_donante = pd.id_producto_donante
            INNER JOIN catalogo_productos cat ON pd.id_catalogo = cat.id_catalogo
            WHERE s.id_receptor = :id_receptor
            GROUP BY cat.id_catalogo, cat.nom_producto
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        $stmtTopProductos->bindParam(':id_receptor', $idReceptor, PDO::PARAM_INT);
        $stmtTopProductos->execute();
        $topProductosData = $stmtTopProductos->fetchAll(PDO::FETCH_ASSOC);

        $topProductos = [
            'labels' => array_column($topProductosData, 'nom_producto') ?: ['Sin datos'],
            'values' => array_map('intval', array_column($topProductosData, 'cantidad')) ?: [0]
        ];

        // Frecuencia mensual de solicitudes (últimos 6 meses)
        $months = 6;
        $current = new \DateTimeImmutable('first day of this month');
        $start = $current->sub(new \DateInterval('P' . ($months - 1) . 'M'));
        $startDate = $start->format('Y-m-01 00:00:00');

        $stmtFrecuencia = $conn->prepare("
            SELECT
                DATE_FORMAT(s.fecha_solicitud, '%Y-%m') as month_key,
                COUNT(*) as cantidad
            FROM solicitud s
            WHERE s.id_receptor = :id_receptor
            AND s.fecha_solicitud >= :start_date
            GROUP BY month_key
            ORDER BY month_key
        ");
        $stmtFrecuencia->bindParam(':id_receptor', $idReceptor, PDO::PARAM_INT);
        $stmtFrecuencia->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmtFrecuencia->execute();

        $data = [];
        foreach ($stmtFrecuencia->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[(string)$row['month_key']] = (int)($row['cantidad'] ?? 0);
        }

        $labels = [];
        $values = [];
        $monthNames = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        for ($i = 0; $i < $months; $i++) {
            $point = $start->add(new \DateInterval('P' . $i . 'M'));
            $key = $point->format('Y-m');
            $labels[] = $monthNames[(int)$point->format('n')];
            $values[] = $data[$key] ?? 0;
        }

        $frecuenciaMensual = [
            'labels' => $labels,
            'values' => $values,
        ];

        return [
            'totalDonaciones' => $totalSolicitudes,
            'totalCantidadDonada' => $alimentosRecibidos,
            'alimentosSalvados' => $alimentosRecibidos,
            'topProductos' => $topProductos,
            'frecuenciaMensual' => $frecuenciaMensual,
        ];
    }
}