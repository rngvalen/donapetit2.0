<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../model/donacion.php';

/**
 * Controlador responsable de la pagina inicial del panel.
 */
class HomeController extends Controller
{
    /**
     * Muestra la vista de bienvenida del sistema.
     *
     * @return void
     */
    public function index(): void
    {
        $userName = $_SESSION['user']['name'] ?? 'Usuario';

        $this->render('home.index', compact('userName'));
    }

    /**
     * Muestra la vista de estadisticas con informacion proveniente de la base de datos.
     *
     * @return void
     */
    public function statics(): void
    {
        $userName = $_SESSION['user']['name'] ?? 'Usuario';
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
