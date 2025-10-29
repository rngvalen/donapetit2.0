<?php
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/StockProductoDonacion.php';

class ProductoController {

    // Mostrar formulario de carga
    public function create() {
        $producto = new Producto();
        $productos = $producto->obtenerTodos();
        require_once __DIR__ . '/../view/products/product_load.php';
    }

    // Guardar nuevo producto disponible para donar
    public function store() {
        session_start();
        if (!isset($_SESSION['id_usuario'])) {
            die("Error: el donante no está identificado.");
        }

        $id_donante = $_SESSION['id_usuario'];

        $producto = new Producto();
        $stock = new StockProductoDonacion();

        // Crear el producto
        $idProducto = $producto->crear(
            $_POST['nom_producto'],
            $_POST['unidad'],
            $_POST['cantidad'],
            $_POST['comentarios'] ?? '',
            $_POST['estado'] ?? 'DISPONIBLE',
            $_POST['categoria_id'] ?? null,
            $id_donante
        );

        // Crear el registro de stock (con vencimiento)
        $stock->crear(
            $idProducto,
            $_POST['cantidad'],
            $_POST['fecha_vencimiento'] ?? null
        );

        header('Location: /productos');
    }

    // Mostrar lista de productos cargados
    public function index() {
        session_start();
        $id_donante = $_SESSION['id_usuario'] ?? null;
        $producto = new Producto();
        $productos = $id_donante ? $producto->obtenerPorDonante($id_donante) : [];
        require_once __DIR__ . '/../view/products/my_products.php';
    }
}
?>
