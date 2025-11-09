<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Solicitud.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/controller.php';

/**
 * Controlador responsable de gestionar las solicitudes de productos.
 */
class SolicitudController extends Controller
{
    private Solicitud $solicitudModel;
    private Producto $productoModel;

    public function __construct()
    {
        $this->solicitudModel = new Solicitud();
        $this->productoModel = new Producto();
    }

    /** 
     * Muestra los productos disponibles para solicitar (vista del receptor)
     */
    public function productosDisponibles(): void
    {
        // Verificar que sea un receptor
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para solicitar productos.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        if ($userRole !== 'receptor') {
            $_SESSION['error'] = 'Solo los receptores pueden solicitar productos.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        // Obtener productos disponibles
        $productos = $this->solicitudModel->obtenerProductosDisponibles(200, 0);

        // Formatear para la vista
        $ofertas = [];
        foreach ($productos as $producto) {
            $ofertas[] = [
                'id_stock' => $producto['id_stock'],
                'nombre' => $producto['nombre_producto'] ?? 'Producto sin nombre',
                'origen' => $producto['nombre_donante'] ?? 'Origen desconocido',
                'cantidad_disponible' => $producto['cantidad_disponible'],
                'unidad' => $producto['unidad'] ?? '',
                'fecha_venc' => $producto['fecha_venc'] ?? null,
                'distancia' => $this->calcularDistancia(
                    $producto['Latitud'] ?? null,
                    $producto['Longitud'] ?? null
                ),
            ];
        }

        $this->render('products.available_products', [
            'ofertas' => $ofertas,
            'titulo' => 'Productos disponibles',
        ]);
    }

    /**
     * Procesa la solicitud de un producto
     */
    public function solicitar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('?controller=Solicitud&action=productosDisponibles');
            return;
        }

        // Verificar sesión
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para solicitar productos.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        if ($userRole !== 'receptor') {
            $_SESSION['error'] = 'Solo los receptores pueden solicitar productos.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idReceptor = (int)$_SESSION['user']['id'];
        $idStock = isset($_POST['id_stock']) ? (int)$_POST['id_stock'] : 0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
        $fechaProgramada = trim($_POST['fecha_programada'] ?? '');

        // Validaciones
        $errores = [];

        if ($idStock <= 0) {
            $errores[] = 'Producto no válido.';
        }

        if ($cantidad <= 0) {
            $errores[] = 'La cantidad debe ser mayor a cero.';
        }

        if ($fechaProgramada === '') {
            $fechaProgramada = null;
        } else {
            $fecha = \DateTime::createFromFormat('Y-m-d', $fechaProgramada);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaProgramada) {
                $errores[] = 'La fecha programada no es válida.';
            }
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('?controller=Solicitud&action=productosDisponibles');
            return;
        }

        try {
            // Crear la solicitud
            $idSolicitud = $this->solicitudModel->crearSolicitud($idReceptor);
            
            // Agregar el detalle
            $this->solicitudModel->agregarDetalle(
                $idSolicitud,
                $idStock,
                $cantidad,
                $fechaProgramada
            );

            $_SESSION['success'] = 'Solicitud enviada exitosamente. El donante la revisará pronto.';
        } catch (\Exception $e) {
            error_log("Error al crear solicitud: " . $e->getMessage());
            $_SESSION['error'] = 'No se pudo procesar la solicitud. Intenta más tarde.';
        }

        $this->redirect('?controller=Solicitud&action=productosDisponibles');
    }

    /**
     * Muestra las solicitudes pendientes del donante
     */
    public function misSolicitudes(): void
    {
        // Verificar que sea un donante
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        if ($userRole !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden ver solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idDonante = (int)$_SESSION['user']['id'];

        // Obtener solicitudes pendientes
        $solicitudes = $this->solicitudModel->solicitudesPendientesPorDonante($idDonante);

        $this->render('products.solicitudes_pendientes', [
            'solicitudes' => $solicitudes,
            'titulo' => 'Solicitudes pendientes',
        ]);
    }

    /**
     * Confirma una solicitud y resta el stock
     */
    public function confirmar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        // Verificar que sea un donante
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        if ($userRole !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden confirmar solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idDonante = (int)$_SESSION['user']['id'];
        $idSolicitudDetalle = isset($_POST['id_solicitud_detalle']) ? (int)$_POST['id_solicitud_detalle'] : 0;

        if ($idSolicitudDetalle <= 0) {
            $_SESSION['error'] = 'Solicitud no válida.';
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        // Confirmar y restar stock
        $resultado = $this->solicitudModel->confirmarSolicitud($idSolicitudDetalle, $idDonante);

        if ($resultado) {
            $_SESSION['success'] = '¡Solicitud confirmada! El stock se ha actualizado correctamente.';
        } else {
            $_SESSION['error'] = 'No se pudo confirmar la solicitud. Verifica que haya stock suficiente.';
        }

        $this->redirect('?controller=Solicitud&action=misSolicitudes');
    }

    /**
     * Rechaza una solicitud
     */
    public function rechazar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        // Verificar que sea un donante
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        if ($userRole !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden rechazar solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idDonante = (int)$_SESSION['user']['id'];
        $idSolicitudDetalle = isset($_POST['id_solicitud_detalle']) ? (int)$_POST['id_solicitud_detalle'] : 0;

        if ($idSolicitudDetalle <= 0) {
            $_SESSION['error'] = 'Solicitud no válida.';
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        // Rechazar solicitud
        $resultado = $this->solicitudModel->rechazarSolicitud($idSolicitudDetalle, $idDonante);

        if ($resultado) {
            $_SESSION['success'] = 'Solicitud rechazada.';
        } else {
            $_SESSION['error'] = 'No se pudo rechazar la solicitud.';
        }

        $this->redirect('?controller=Solicitud&action=misSolicitudes');
    }

    /**
     * Calcula la distancia aproximada basada en coordenadas
     * Por ahora retorna una distancia ficticia, pero puedes implementar
     * la fórmula de Haversine si tienes las coordenadas del receptor
     */
    private function calcularDistancia(?float $lat, ?float $lon): string
    {
        // TODO: Implementar cálculo real de distancia usando coordenadas del usuario
        $distancia = rand(5, 50) / 10; // 0.5 a 5.0 km
        return number_format($distancia, 1, '.', '') . ' km';
    }
}