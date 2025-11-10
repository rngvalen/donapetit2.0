<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Catalogo.php';
require_once __DIR__ . '/../model/ProductoDonante.php';
require_once __DIR__ . '/../model/Categoria.php';
require_once __DIR__ . '/controller.php';

/**
 * Controlador responsable de las operaciones sobre productos del donante.
 */
class ProductoController extends Controller
{
    private Catalogo $catalogoModel;
    private ProductoDonante $productoDonante;
    private Categoria $categoriaModel;

    public function __construct()
    {
        $this->catalogoModel = new Catalogo();
        $this->productoDonante = new ProductoDonante();
        $this->categoriaModel = new Categoria();
    }

    /**
     * Redirige a misProductos (para compatibilidad)
     */
    public function index(): void
    {
        $this->redirect('?controller=Producto&action=misProductos');
    }

    /**
     * Muestra el inventario del donante (sus productos cargados)
     */
    public function misProductos(): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        $userId = (int)$_SESSION['user']['id'];

        if ($userRole !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden ver su inventario.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $productos = $this->productoDonante->obtenerInventarioDonante($userId);

        $this->render('products.my_products', [
            'productos' => $productos,
            'titulo' => 'Mi inventario',
        ]);
    }

    /**
     * Muestra el formulario para cargar productos del catálogo
     */
    public function create(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden cargar productos.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $productos = $this->catalogoModel->obtenerProductosActivos();

        $this->render('products.product_load', [
            'productos' => $productos
        ]);
    }

    /**
     * Guarda un producto en el inventario del donante
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=Producto&action=create');
            return;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'donante') {
            $_SESSION['error'] = 'Solo los donantes pueden cargar productos.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $idUsuario = (int)$_SESSION['user']['id'];
        $idCatalogo = (int)($_POST['id_catalogo'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $fechaVencimiento = trim($_POST['fecha_vencimiento'] ?? '');

        $errores = [];

        // Validar producto seleccionado
        if ($idCatalogo <= 0) {
            $errores[] = 'Debes seleccionar un producto del catálogo.';
        }

        // Validar cantidad
        if ($cantidad <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0.';
        }

        // Validar fecha de vencimiento (opcional)
        if ($fechaVencimiento !== '') {
            $fecha = \DateTime::createFromFormat('Y-m-d', $fechaVencimiento);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaVencimiento) {
                $errores[] = 'La fecha de vencimiento no es válida.';
            } elseif ($fecha < new \DateTime('today')) {
                $errores[] = 'La fecha de vencimiento debe ser futura.';
            }
        } else {
            $fechaVencimiento = null;
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('?controller=Producto&action=create');
            return;
        }

        try {
            $this->productoDonante->registrarProducto(
                $idUsuario,
                $idCatalogo,
                $cantidad,
                $fechaVencimiento
            );
            
            $_SESSION['success'] = 'Producto agregado exitosamente a tu inventario.';
            $this->redirect('?controller=Producto&action=misProductos');
            
        } catch (\Throwable $e) {
            error_log("Error al registrar producto: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar el producto: ' . $e->getMessage();
            $this->redirect('?controller=Producto&action=create');
        }
    }

    /**
     * Elimina un producto del inventario del donante
     */
    public function destroy(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id > 0 && $this->productoDonante->eliminarProducto($id)) {
            $_SESSION['success'] = 'Producto eliminado de tu inventario.';
        } else {
            $_SESSION['error'] = 'No se pudo eliminar el producto.';
        }

        $this->redirect('?controller=Producto&action=misProductos');
    }

    /**
     * Muestra el formulario de edición de un producto del inventario
     */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        // Obtener el producto del inventario
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $idUsuario = (int)$_SESSION['user']['id'];
        $productos = $this->productoDonante->obtenerInventarioDonante($idUsuario);
        
        $producto = null;
        foreach ($productos as $p) {
            if ((int)$p['id_producto_donante'] === $id) {
                $producto = $p;
                break;
            }
        }

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        $this->render('products.edit', [
            'producto' => $producto
        ]);
    }

    /**
     * Actualiza la cantidad de un producto en el inventario
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=Producto&action=misProductos');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);

        $errores = [];

        if ($id <= 0) {
            $errores[] = 'Producto no válido.';
        }

        if ($cantidad <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0.';
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('?controller=Producto&action=edit&id=' . $id);
            return;
        }

        try {
            $this->productoDonante->actualizarCantidad($id, $cantidad);
            $_SESSION['success'] = 'Producto actualizado correctamente.';
        } catch (\Throwable $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            $_SESSION['error'] = 'Error al actualizar el producto.';
        }

        $this->redirect('?controller=Producto&action=misProductos');
    }

    /**
     * Muestra productos disponibles para receptores
     */
    public function productosDisponibles(): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión.';
            $this->redirect('?controller=Auth&action=mostrarLogin');
            return;
        }

        $userRole = $_SESSION['user']['rol'] ?? '';
        
        if ($userRole !== 'receptor') {
            $_SESSION['error'] = 'Solo los receptores pueden ver productos disponibles.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        // Obtener productos disponibles usando el modelo
        $productos = $this->productoDonante->obtenerTodosDisponibles();

        $this->render('products.available_products', [
            'productos' => $productos,
            'titulo' => 'Productos disponibles para solicitar'
        ]);
    }

    /**
     * Catálogo administrativo - solo para admin
     */
    public function catalogo(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
            $_SESSION['error'] = 'Acceso no autorizado.';
            $this->redirect('?controller=Home&action=index');
            return;
        }

        $productos = $this->catalogoModel->obtenerProductosActivos();
        
        $this->render('admin.catalogo_de_productos', [
            'productos' => $productos
        ]);
    }

    /**
 * Muestra el formulario para solicitar un producto (placeholder)
 */
public function solicitar(): void
{
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = 'Debes iniciar sesión.';
        $this->redirect('?controller=Auth&action=mostrarLogin');
        return;
    }

    $userRole = $_SESSION['user']['rol'] ?? '';
    
    if ($userRole !== 'receptor') {
        $_SESSION['error'] = 'Solo los receptores pueden solicitar productos.';
        $this->redirect('?controller=Home&action=index');
        return;
    }

    $_SESSION['info'] = 'La funcionalidad de solicitudes estará disponible próximamente.';
    $this->redirect('?controller=Producto&action=productosDisponibles');
}
}