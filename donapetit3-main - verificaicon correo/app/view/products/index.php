<?php
/** 
 * Vista de listado de productos.
 * 
 * Variables esperadas:
 * - $productos: array con los productos a mostrar.
 * - $page: número de página actual.
 * 
 * También muestra mensajes de éxito o error almacenados en $_SESSION.
 */

// Obtiene el número de página actual, por defecto 1 si no está definido
$page = $page ?? 1;

// Recupera mensajes de éxito y error de la sesión, si existen, y los elimina para no mostrarlos de nuevo
$successMessage = $_SESSION['success'] ?? null;
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<section class="py-8">
  <!-- Encabezado con título, número de página y botón para crear un nuevo producto -->
  <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold">Productos</h1>
      <p class="text-sm text-slate-600">Pagina <?php echo (int)$page; ?></p>
    </div>
    <!-- Botón para ir al formulario de creación de producto -->
    <a href="index.php?controller=Producto&action=create"
       class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-white shadow hover:bg-emerald-700">
      <span>Nuevo producto</span>
    </a>
  </header>

  <!-- Muestra mensaje de éxito si existe -->
  <?php if ($successMessage): ?>
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
      <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <!-- Muestra mensaje de error si existe -->
  <?php if ($errorMessage): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm">
      <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <!-- Si hay productos, muestra la tabla -->
  <?php if (!empty($productos)): ?>
    <div class="overflow-x-auto shadow-sm">
      <table class="min-w-full divide-y divide-slate-200 bg-white">
        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
          <tr>
            <th scope="col" class="px-4 py-3 text-left">ID</th>
            <th scope="col" class="px-4 py-3 text-left">Nombre</th>
            <th scope="col" class="px-4 py-3 text-left">Unidad</th>
            <th scope="col" class="px-4 py-3 text-left">Categoria</th>
            <th scope="col" class="px-4 py-3 text-right">Cantidad</th>
            <th scope="col" class="px-4 py-3 text-left">Vencimiento</th>
            <th scope="col" class="px-4 py-3 text-left">Estado</th>
            <th scope="col" class="px-4 py-3 text-left">Comentarios</th>
            <th scope="col" class="px-4 py-3 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-sm">
          <!-- Itera sobre cada producto y muestra sus datos en una fila -->
          <?php foreach ($productos as $producto): ?>
            <tr>
              <td class="px-4 py-3 font-mono text-xs">
                <?php echo htmlspecialchars((string)($producto['id_producto'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3">
                <?php echo htmlspecialchars((string)($producto['nom_producto'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3">
                <?php echo htmlspecialchars((string)($producto['unidad'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3">
                <?php echo htmlspecialchars((string)($producto['categoria'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3 text-right">
                <?php echo htmlspecialchars((string)($producto['cantidad'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3">
                <?php echo htmlspecialchars((string)($producto['fecha_vencimiento'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3">
                <?php echo htmlspecialchars((string)($producto['estado'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3">
                <?php echo htmlspecialchars((string)($producto['comentarios'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-4 py-3 text-right">
                <!-- Acciones: Ver, Editar, Eliminar -->
                <a class="text-sky-600 hover:underline mr-3" href="index.php?controller=Producto&action=show&id=<?php echo urlencode((string)($producto['id_producto'] ?? '')); ?>">
                  Ver
                </a>
                <a class="text-amber-600 hover:underline mr-3" href="index.php?controller=Producto&action=edit&id=<?php echo urlencode((string)($producto['id_producto'] ?? '')); ?>">
                  Editar
                </a>
                <a class="text-red-600 hover:underline" href="index.php?controller=Producto&action=destroy&id=<?php echo urlencode((string)($producto['id_producto'] ?? '')); ?>"
                   onclick="return confirm('Eliminar producto?');">
                  Eliminar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <!-- Si no hay productos, muestra un mensaje -->
    <div class="rounded-lg border border-slate-200 bg-white px-4 py-6 text-sm text-slate-600">
      No hay productos cargados aun.
    </div>
  <?php endif; ?>
</section>
