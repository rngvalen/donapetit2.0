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
     *
     * @return void
     */
    public function statics(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=Auth&action=mostrarLogin');
            exit;
        }

        $userName = $_SESSION['user']['name'];
        $stats = $this->buildStatisticsContext();

        $this->render('statics.statics_main', array_merge($stats, compact('userName')));
    }

    /**
     * Obtiene los datos necesarios para el tablero de estadisticas.
     *
     * @return array<string,mixed>
     */
    private function buildStatisticsContext(): array
    {
        $donacionModel = new Donacion();

        $totalRegistroDonaciones = $donacionModel->totalRegistros();
        $totalCantidadDonada = $donacionModel->totalCantidad();
        $alimentosSalvados = $donacionModel->totalPorEstado('ENTREGADO');

        if ($alimentosSalvados === 0) {
            $alimentosSalvados = $totalCantidadDonada;
        }

        $topProductos = $donacionModel->topProductos();
        $frecuenciaMensual = $donacionModel->frecuenciaMensual();

        return [
            'totalDonaciones' => $totalRegistroDonaciones,
            'totalCantidadDonada' => $totalCantidadDonada,
            'alimentosSalvados' => $alimentosSalvados,
            'topProductos' => $topProductos,
            'frecuenciaMensual' => $frecuenciaMensual,
        ];
    }
}