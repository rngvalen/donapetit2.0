<?php
declare(strict_types=1);

require_once __DIR__ . '/controller.php';

/**
 * Controlador para la vista del mapa de donantes.
 *
 * Renderiza la vista Tailwind con Leaflet y configuración del mapa.
 */
class MapController extends Controller
{
    /**
     * Muestra el mapa con configuración por defecto.
     *
     * @return void
     */
    public function index(): void
    {
        $mapConfig = [
            'center' => ['lat' => -34.6037, 'lng' => -58.3816],
            'zoom' => 14,
            'radius' => 2.5,
            'radiusMin' => 0.5,
            'radiusMax' => 5,
            'radiusStep' => 0.5,
            'businesses' => [
                ['nombre' => 'Supermercado El Molino', 'distancia' => '820 m', 'detalle' => '5 productos disponibles', 'estado' => 'online'],
                ['nombre' => 'Supermercado La Esquina', 'distancia' => '950 m', 'detalle' => '10 productos disponibles', 'estado' => 'online'],
                ['nombre' => 'Supermercado Don Pedro', 'distancia' => '1.4 km', 'detalle' => 'Sin stock', 'estado' => 'offline'],
            ],
        ];

        $this->render('statics.map', compact('mapConfig'));
    }
// aca



    public function notificationSettings(): void //
    {
        $radioMin = 1;
        $radioMax = 20;
        $radioActual = 5;
        $canales = [
            'push' => true,
            'email' => true,
        ];

        $this->render('admin.notification_settings', compact('radioMin', 'radioMax', 'radioActual', 'canales'));
    }


}

