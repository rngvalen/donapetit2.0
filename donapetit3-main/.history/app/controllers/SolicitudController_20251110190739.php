<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Solicitud.php';
require_once __DIR__ . '/../model/ProductoDonante.php';
require_once __DIR__ . '/controller.php';

class SolicitudController extends Controller
{
    private Solicitud $solicitudModel;
    private ProductoDonante $productoDonante;

    public function __construct()
    {
        $this->solicitudModel = new Solicitud();
        $this->productoDonante = new ProductoDonante();
    }

    /**
     * Muestra el formulario para crear una solicitud
     */
    public function crear(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'receptor') {
            $_SESSION['error'] = 'Solo los receptores pueden crear solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idDonante = (int)($_GET['id_donante'] ?? 0);
        
        if ($idDonante <= 0) {
            $_SESSION['error'] = 'Donante no especificado.';
            $this->redirect('?controller=Producto&action=productosDisponibles');
            return;
        }

        // Obtener productos disponibles del donante
        $productos = $this->productoDonante->obtenerInventarioDonante($idDonante);

        // Obtener nombre del donante
        $nombreDonante = 'Donante';
        if (!empty($productos)) {
            // Buscar el nombre comercial (tendremos que agregarlo al query)
            Model::initDb();
            $sql = "SELECT d.nom_comercial, u.Nombre 
                    FROM donante d
                    INNER JOIN usuario u ON d.id_usu_donante = u.id_usuario
                    WHERE d.id_usu_donante = :id";
            $stmt = Model::getDb()->prepare($sql);
            $stmt->execute([':id' => $idDonante]);
            $donante = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($donante) {
                $nombreDonante = $donante['nom_comercial'] ?: $donante['Nombre'];
            }
        }

        $this->render('solicitudes.crear', [
            'productos' => $productos,
            'id_donante' => $idDonante,
            'nombre_donante' => $nombreDonante
        ]);
    }

    /**
     * Guarda una nueva solicitud
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=Producto&action=productosDisponibles');
            return;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'receptor') {
            $_SESSION['error'] = 'Solo los receptores pueden crear solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idReceptor = (int)$_SESSION['user']['id'];
        $idDonante = (int)($_POST['id_donante'] ?? 0);
        $productos = $_POST['productos'] ?? [];
        $comentarios = trim($_POST['comentarios'] ?? '');

        $errores = [];

        if ($idDonante <= 0) {
            $errores[] = 'Donante no válido.';
        }

        if (empty($productos)) {
            $errores[] = 'Debes seleccionar al menos un producto.';
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('?controller=Solicitud&action=crear&id_donante=' . $idDonante);
            return;
        }

        try {
            // Crear solicitud
            $idSolicitud = $this->solicitudModel->crearSolicitud(
                $idReceptor,
                $idDonante,
                $comentarios !== '' ? $comentarios : null
            );

            // Agregar productos a la solicitud
            foreach ($productos as $idProductoDonante => $cantidad) {
                $cantidad = (int)$cantidad;
                if ($cantidad > 0) {
                    $this->solicitudModel->agregarProducto(
                        $idSolicitud,
                        (int)$idProductoDonante,
                        $cantidad
                    );
                }
            }

            $_SESSION['success'] = 'Solicitud enviada exitosamente al donante.';
            $this->redirect('?controller=Solicitud&action=misSolicitudes');

        } catch (\Throwable $e) {
            error_log("Error al crear solicitud: " . $e->getMessage());
            $_SESSION['error'] = 'Error al crear la solicitud: ' . $e->getMessage();
            $this->redirect('?controller=Solicitud&action=crear&id_donante=' . $idDonante);
        }
    }

    /**
     * Lista las solicitudes del receptor
     */
    public function misSolicitudes(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'receptor') {
            $_SESSION['error'] = 'Solo los receptores pueden ver sus solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idReceptor = (int)$_SESSION['user']['id'];
        $solicitudes = $this->solicitudModel->obtenerSolicitudesReceptor($idReceptor);

        $this->render('solicitudes.mis_solicitudes', [
            'solicitudes' => $solicitudes,
            'titulo' => 'Mis solicitudes'
        ]);
    }

    /**
     * Lista las solicitudes recibidas por el donante
     */
    public function solicitudesRecibidas(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden ver solicitudes recibidas.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idDonante = (int)$_SESSION['user']['id'];
        $solicitudes = $this->solicitudModel->obtenerSolicitudesDonante($idDonante);

        $this->render('solicitudes.recibidas', [
            'solicitudes' => $solicitudes,
            'titulo' => 'Solicitudes recibidas'
        ]);
    }

    /**
     * Muestra el detalle de una solicitud
     */
    public function ver(): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $idSolicitud = (int)($_GET['id'] ?? 0);
        
        if ($idSolicitud <= 0) {
            $_SESSION['error'] = 'Solicitud no encontrada.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $solicitud = $this->solicitudModel->obtenerDetalleSolicitud($idSolicitud);

        if (!$solicitud) {
            $_SESSION['error'] = 'Solicitud no encontrada.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $this->render('solicitudes.detalle', [
            'solicitud' => $solicitud
        ]);
    }

    /**
     * Aprueba una solicitud y registra la entrega
     */
    public function aprobar(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden aprobar solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idSolicitud = (int)($_GET['id'] ?? 0);

        if ($idSolicitud <= 0) {
            $_SESSION['error'] = 'Solicitud no válida.';
            $this->redirect('?controller=Solicitud&action=solicitudesRecibidas');
            return;
        }

        if ($this->solicitudModel->registrarEntrega($idSolicitud)) {
            $_SESSION['success'] = 'Solicitud aprobada. El stock se actualizó automáticamente.';
        } else {
            $_SESSION['error'] = 'Error al aprobar la solicitud.';
        }

        $this->redirect('?controller=Solicitud&action=solicitudesRecibidas');
    }

    /**
     * Rechaza una solicitud
     */
    public function rechazar(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden rechazar solicitudes.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idSolicitud = (int)($_GET['id'] ?? 0);

        if ($idSolicitud <= 0) {
            $_SESSION['error'] = 'Solicitud no válida.';
            $this->redirect('?controller=Solicitud&action=solicitudesRecibidas');
            return;
        }

        if ($this->solicitudModel->cambiarEstado($idSolicitud, 'Rechazada')) {
            $_SESSION['success'] = 'Solicitud rechazada.';
        } else {
            $_SESSION['error'] = 'Error al rechazar la solicitud.';
        }

        $this->redirect('?controller=Solicitud&action=solicitudesRecibidas');
    }
}