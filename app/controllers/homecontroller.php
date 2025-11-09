<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../core/auth_session.php';
require_once __DIR__ . '/../model/donacion.php';
require_once __DIR__ . '/../services/ProfileService.php';

/**
 * Controlador responsable de la pagina inicial del panel.
 */
class HomeController extends Controller
{
    private ProfileService $profiles;

    public function __construct()
    {
        $this->profiles = new ProfileService();
    }

    /**
     * Muestra la vista de bienvenida del sistema.
     *
     * @return void
     */
    public function index(): void
    {
        requireLogin();

        $usuario = current_user() ?? [];
        $userName = $usuario['name'] ?? 'Usuario';
        $userRole = normalize_role($usuario['rol'] ?? null) ?? ROLE_DONANTE;
        $userId = (int)($usuario['id'] ?? 0);

        if ($userId > 0 && $this->profiles->needsCompletion($userId, $userRole)) {
            $_SESSION['profile_pending'] = true;
            $this->redirect('?controller=Profile&action=completarPerfil');
        }

        unset($_SESSION['profile_pending']);

        if ($userRole === ROLE_RECEPTOR) {
            $this->render('home.receptor', [
                'userName' => $userName,
                'userRole' => $userRole,
            ]);
            return;
        }

        $this->render('home.index', [
            'userName' => $userName,
            'userRole' => $userRole,
        ]);
    }

    /**
     * Muestra la vista de estadisticas con informacion proveniente de la base de datos.
     *
     * @return void
     */
    public function statics(): void
    {
        requireLogin();
        $usuario = current_user() ?? [];
        $userName = $usuario['name'] ?? 'Usuario';
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
