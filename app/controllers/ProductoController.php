<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/Unidad.php';
require_once __DIR__ . '/../model/Categoria.php';
require_once __DIR__ . '/controller.php';

/**
 * Controlador responsable de las operaciones CRUD sobre productos.
 */
class ProductoController extends Controller
{
    /**
     * Instancia del modelo Producto utilizada para las operaciones de escritura.
     */
    private Producto $productoModel;
    private Categoria $categoriaModel;

    /**
     * Prepara el controlador instanciando el modelo necesario.
     */
    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->categoriaModel = new Categoria();
    }

    /**
     * Retorna mapa de categorias [id => nombre].
     *
     * @return array<int,string>
     */
    private function getCategoriasMap(): array
    {
        $categorias = $this->categoriaModel->todas();
        $map = [];
        foreach ($categorias as $categoria) {
            $id = (int)($categoria['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $map[$id] = (string)($categoria['nombre'] ?? '');
        }

        return $map;
    }

    /**
     * Muestra un listado paginado de productos cargados por el usuario.
     *
     * @return void
     */
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $productos = Producto::all($limit, $offset);
        $this->render('products.index', compact('productos', 'page'));
    }

    /**
     * Renderiza el catalogo administrativo con filtros y ordenamientos.
     *
     * @return void
     */
    public function catalogo(): void
    {
        $filters = [
            'search' => isset($_GET['search']) ? trim((string)$_GET['search']) : '',
            'estado' => isset($_GET['estado']) ? trim((string)$_GET['estado']) : '',
            'order' => isset($_GET['order']) && $_GET['order'] !== '' ? (string)$_GET['order'] : 'recent',
        ];

        $estadoOpciones = ['Disponible', 'Agotado', 'En revision'];

        $productos = Producto::all(500, 0);

        $unidadModel = new Unidad();
        $unidades = $unidadModel->activas();
        $nombresDisponibles = $this->productoModel->obtenerNombresDisponibles();
        $categorias = $this->getCategoriasMap();

        $this->render(
            'admin.catalogo_de_productos',
            compact('productos', 'filters', 'estadoOpciones', 'unidades', 'nombresDisponibles', 'categorias')
        );
    }

    /**
     * Presenta el formulario para crear un nuevo producto.
     *
     * @return void
     */
    public function create(): void
    {
        $unidadModel = new Unidad();
        $unidades = $unidadModel->activas();
        $nombresDisponibles = $this->productoModel->obtenerNombresDisponibles();
        $categorias = $this->getCategoriasMap();
        $this->render('products.product_load', compact('unidades', 'nombresDisponibles', 'categorias'));
    }

    /**
     * Procesa la solicitud de creacion de un nuevo producto.
     *
     * @return void
     */
    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('index.php?controller=Producto&action=create');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $unidad = trim($_POST['unidad'] ?? '');
        $cantidad = $_POST['cantidad'] ?? null;
        $fechaVencimiento = trim($_POST['vencimiento'] ?? '');
        $comentarios = trim($_POST['comentarios'] ?? '');
        $categoriaIdRaw = $_POST['categoria'] ?? '';

        $errores = [];

        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio.';
        }
        if ($nombre !== '') {
            $existentes = $this->productoModel->obtenerNombresDisponibles();
            $toLower = static function (string $value): string {
                return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
            };
            $lowerNombre = $toLower($nombre);
            $estaCatalogo = false;
            foreach ($existentes as $existente) {
                if ($toLower($existente) === $lowerNombre) {
                    $estaCatalogo = true;
                    break;
                }
            }
            if (!$estaCatalogo) {
                $errores[] = 'El producto seleccionado no existe en el catalogo. Consulta con administracion.';
            }
        }
        if ($unidad === '') {
            $errores[] = 'La unidad es obligatoria.';
        }

        if ($cantidad === '' || $cantidad === null) {
            $cantidad = null;
        } else {
            if (!ctype_digit((string)$cantidad) || (int)$cantidad <= 0) {
                $errores[] = 'La cantidad debe ser un entero positivo.';
            } else {
                $cantidad = (int)$cantidad;
            }
        }

        if ($fechaVencimiento === '') {
            $fechaVencimiento = null;
        } else {
            $fecha = \DateTime::createFromFormat('Y-m-d', $fechaVencimiento);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaVencimiento) {
                $errores[] = 'La fecha de vencimiento no tiene un formato valido (AAAA-MM-DD).';
            }
        }

        if ($comentarios === '') {
            $comentarios = null;
        }

        $categoriasMap = $this->getCategoriasMap();
        $categoriaId = null;
        $categoriaNombre = null;
        if ($categoriaIdRaw === '' || $categoriaIdRaw === null) {
            $errores[] = 'La categoria es obligatoria.';
        } elseif (!ctype_digit((string)$categoriaIdRaw)) {
            $errores[] = 'Categoria invalida.';
        } else {
            $categoriaId = (int)$categoriaIdRaw;
            if (isset($categoriasMap[$categoriaId])) {
                $categoriaNombre = $categoriasMap[$categoriaId];
            } else {
                $errores[] = 'La categoria seleccionada no existe.';
            }
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('index.php?controller=Producto&action=create');
        }

        try {
            $id = $this->productoModel->crear(
                $nombre,
                $unidad,
                $cantidad,
                $fechaVencimiento,
                $comentarios,
                null,
                $categoriaId,
                $categoriaNombre
            );
            $_SESSION['success'] = 'Producto creado con exito (ID: ' . $id . ').';
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'No se pudo crear el producto. Intenta mas tarde.';
        }

        $this->redirect('index.php?controller=Producto&action=index');
    }

    /**
     * Muestra el detalle de un producto especifico.
     *
     * @return void
     */
    public function show(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('index.php?controller=Producto&action=index');
        }

        $producto = $this->productoModel->encontrarPorId($id);
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirect('index.php?controller=Producto&action=index');
        }

        $this->render('products.show', compact('producto'));
    }

    /**
     * Renderiza el formulario de edicion para un producto existente.
     *
     * @return void
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('index.php?controller=Producto&action=index');
        }

        $producto = $this->productoModel->encontrarPorId($id);
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirect('index.php?controller=Producto&action=index');
        }
        $unidadModel = new Unidad();
        $unidades = $unidadModel->activas();
        $nombresDisponibles = $this->productoModel->obtenerNombresDisponibles();
        $categorias = $this->getCategoriasMap();
        $this->render('products.edit', compact('producto', 'unidades', 'nombresDisponibles', 'categorias'));
    }

    /**
     * Actualiza un producto existente con los datos enviados por el formulario.
     *
     * @return void
     */
    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('index.php?controller=Producto&action=index');
        }

        $id = $_POST['id'] ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $unidad = trim($_POST['unidad'] ?? '');
        $cantidad = $_POST['cantidad'] ?? null;
        $fechaVencimiento = trim($_POST['vencimiento'] ?? '');
        $comentarios = trim($_POST['comentarios'] ?? '');
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : null;
        $categoriaIdRaw = $_POST['categoria'] ?? '';

        if (!$id) {
            $_SESSION['error'] = 'Identificador de producto invalido.';
            $this->redirect('index.php?controller=Producto&action=index');
        }

        $errores = [];

        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio.';
        }
        if ($nombre !== '') {
            $existentes = $this->productoModel->obtenerNombresDisponibles();
            $toLower = static function (string $value): string {
                return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
            };
            $lowerNombre = $toLower($nombre);
            $estaCatalogo = false;
            foreach ($existentes as $existente) {
                if ($toLower($existente) === $lowerNombre) {
                    $estaCatalogo = true;
                    break;
                }
            }
            if (!$estaCatalogo) {
                $errores[] = 'El producto seleccionado no existe en el catalogo. Consulta con administracion.';
            }
        }
        if ($unidad === '') {
            $errores[] = 'La unidad es obligatoria.';
        }

        if ($cantidad === '' || $cantidad === null) {
            $cantidad = null;
        } else {
            if (!ctype_digit((string)$cantidad) || (int)$cantidad <= 0) {
                $errores[] = 'La cantidad debe ser un entero positivo.';
            } else {
                $cantidad = (int)$cantidad;
            }
        }

        if ($fechaVencimiento === '') {
            $fechaVencimiento = null;
        } else {
            $fecha = \DateTime::createFromFormat('Y-m-d', $fechaVencimiento);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaVencimiento) {
                $errores[] = 'La fecha de vencimiento no tiene un formato valido (AAAA-MM-DD).';
            }
        }

        if ($comentarios === '') {
            $comentarios = null;
        }

        if ($estado === '') {
            $estado = null;
        }

        $categoriasMap = $this->getCategoriasMap();
        $categoriaId = null;
        $categoriaNombre = null;
        if ($categoriaIdRaw === '' || $categoriaIdRaw === null) {
            $errores[] = 'La categoria es obligatoria.';
        } elseif (!ctype_digit((string)$categoriaIdRaw)) {
            $errores[] = 'Categoria invalida.';
        } else {
            $categoriaId = (int)$categoriaIdRaw;
            if (isset($categoriasMap[$categoriaId])) {
                $categoriaNombre = $categoriasMap[$categoriaId];
            } else {
                $errores[] = 'La categoria seleccionada no existe.';
            }
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('index.php?controller=Producto&action=edit&id=' . urlencode((string)$id));
        }

        $ok = $this->productoModel->actualizarProducto(
            $id,
            $nombre,
            $unidad,
            $cantidad,
            $fechaVencimiento,
            $comentarios,
            $estado,
            $categoriaId,
            $categoriaNombre
        );

        $_SESSION['success'] = $ok ? 'Producto actualizado.' : 'No se pudo actualizar.';
        $this->redirect('index.php?controller=Producto&action=index');
    }

    /**
     * Elimina un producto por su identificador.
     *
     * @return void
     */
    public function destroy(): void
    {
        $id = $_GET['id'] ?? null;
        if ($id && $this->productoModel->eliminarPorId($id)) {
            $_SESSION['success'] = 'Producto eliminado.';
        } else {
            $_SESSION['error'] = 'No se pudo eliminar el producto.';
        }

        $this->redirect('index.php?controller=Producto&action=index');
    }

    /**
     * Permite al administrador agregar un nuevo producto base al catalogo.
     */
    public function storeCatalogItem(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('index.php?controller=Producto&action=catalogo');
        }

        $nombre = trim($_POST['nombre_catalogo'] ?? '');
        $unidad = trim($_POST['unidad_catalogo'] ?? '');
        $comentarios = trim($_POST['comentarios_catalogo'] ?? '');
        $categoriaIdRaw = $_POST['categoria_catalogo'] ?? '';

        $errores = [];

        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio.';
        }

        if ($unidad === '') {
            $errores[] = 'Debes seleccionar una unidad.';
        }

        $existentes = $this->productoModel->obtenerNombresDisponibles();
        $toLower = static function (string $value): string {
            return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
        };
        $lowerNombre = $toLower($nombre);
        foreach ($existentes as $existente) {
            if ($toLower($existente) === $lowerNombre) {
                $errores[] = 'Ya existe un producto con ese nombre en el catalogo.';
                break;
            }
        }

        $categoriasMap = $this->getCategoriasMap();
        $categoriaId = null;
        $categoriaNombre = null;
        if ($categoriaIdRaw === '' || $categoriaIdRaw === null) {
            $errores[] = 'Debes seleccionar una categoria.';
        } elseif (!ctype_digit((string)$categoriaIdRaw)) {
            $errores[] = 'Categoria invalida.';
        } else {
            $categoriaId = (int)$categoriaIdRaw;
            if (isset($categoriasMap[$categoriaId])) {
                $categoriaNombre = $categoriasMap[$categoriaId];
            } else {
                $errores[] = 'La categoria seleccionada no existe.';
            }
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode(' ', $errores);
            $this->redirect('index.php?controller=Producto&action=catalogo');
        }

        try {
            $this->productoModel->crear(
                $nombre,
                $unidad,
                null,
                null,
                $comentarios === '' ? null : $comentarios,
                'DISPONIBLE',
                $categoriaId,
                $categoriaNombre
            );
            $_SESSION['success'] = 'Producto agregado al catalogo.';
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'No se pudo agregar el producto al catalogo.';
        }

        $this->redirect('index.php?controller=Producto&action=catalogo');
    }
        // Normaliza y valida los datos de un producto. lo movimos de adminController hasta aca
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
        // Procesar fecha de vencimiento 
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


    private function filterLowStock(array $productos): array
    {
        $filtrados = array_filter(
            $productos,
            static function (array $producto): bool {
                $cantidad = $producto['cantidad'] ?? null;
                return $cantidad !== null && $cantidad <= 5;
            }
        );

        usort(
            $filtrados,
            static function (array $a, array $b): int {
                $cantidadA = $a['cantidad'] ?? PHP_INT_MAX;
                $cantidadB = $b['cantidad'] ?? PHP_INT_MAX;

                if ($cantidadA === $cantidadB) {
                    return strcmp($a['nombre'] ?? '', $b['nombre'] ?? '');
                }

                return $cantidadA <=> $cantidadB;
            }
        );

        return $filtrados;
    }

    /**
     * Selecciona los productos que vencen en los proximos siete dias.
     *
     * @param array<int,array<string,mixed>> $productos Coleccion normalizada.
     * @return array<int,array<string,mixed>> Productos con fecha proxima.
     */
    private function filterExpiring(array $productos): array
    {
        $today = new \DateTimeImmutable('today');
        $limit = $today->modify('+7 days');

        $filtrados = array_filter(
            $productos,
            static function (array $producto) use ($today, $limit): bool {
                $fecha = $producto['fecha_vencimiento'] ?? null;

                if (!$fecha instanceof \DateTimeImmutable) {
                    return false;
                }

                if ($fecha < $today) {
                    return false;
                }

                return $fecha <= $limit;
            }
        );

        usort(
            $filtrados,
            static function (array $a, array $b): int {
                $fechaA = $a['fecha_vencimiento'] ?? null;
                $fechaB = $b['fecha_vencimiento'] ?? null;

                if ($fechaA instanceof \DateTimeImmutable && $fechaB instanceof \DateTimeImmutable) {
                    return $fechaA <=> $fechaB;
                }

                return strcmp($a['nombre'] ?? '', $b['nombre'] ?? '');
            }
        );

        return $filtrados;
    }

}
