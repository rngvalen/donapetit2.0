<?php
$solicitud = $solicitud ?? null;

if (!$solicitud) {
    echo '<p class="text-center text-red-600">Solicitud no encontrada.</p>';
    return;
}

$productos = $solicitud['productos'] ?? [];

// Determinar color según estado
$estadoClasses = [
    'Pendiente' => 'bg-yellow-100 text-yellow-800',
    'Aprobada' => 'bg-green-100 text-green-800',
    'Rechazada' => 'bg-red-100 text-red-800'
];
$estadoClass = $estadoClasses[$solicitud['estado']] ?? 'bg-slate-100 text-slate-800';
?>
<section class="max-w-5xl mx-auto px-4 py-8">
  
  <div class="mb-6">
    <a href="javascript:history.back()" 
       class="inline-flex items-center gap-2 text-blue-600 hover:underline text-sm font-medium">
      ← Volver
    </a>
  </div>

  <header class="mb-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-slate-900">Solicitud #<?= $solicitud['id_solicitud'] ?></h1>
      <span class="px-4 py-2 <?= $estadoClass ?> text-sm font-semibold rounded-full">
        <?= htmlspecialchars($solicitud['estado']) ?>
      </span>
    </div>
    <p class="text-sm text-slate-500 mt-2">
      📅 <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?>
    </p>
  </header>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    
    <!-- Información del receptor -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <h3 class="font-bold text-slate-900 mb-4">🏢 Receptor</h3>
      <div class="space-y-2 text-sm">
        <p><strong>Institución:</strong> <?= htmlspecialchars($solicitud['nom_institucion']) ?></p>
        <p><strong>RENACOM:</strong> <?= htmlspecialchars($solicitud['num_renacom']) ?></p>
        <p><strong>Contacto:</strong> <?= htmlspecialchars($solicitud['nombre_receptor']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($solicitud['email_receptor']) ?></p>
      </div>
    </div>

    <!-- Información del donante -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <h3 class="font-bold text-slate-900 mb-4">🏪 Donante</h3>
      <div class="space-y-2 text-sm">
        <p><strong>Comercio:</strong> <?= htmlspecialchars($solicitud['nom_comercial']) ?></p>
        <p><strong>CUIT:</strong> <?= htmlspecialchars($solicitud['CUIT']) ?></p>
        <p><strong>Contacto:</strong> <?= htmlspecialchars($solicitud['nombre_donante']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($solicitud['email_donante']) ?></p>
      </div>
    </div>

  </div>

  <!-- Observaciones -->
  <?php if ($solicitud['observacion']): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8">
      <h3 class="font-bold text-slate-900 mb-2">💬 Observaciones</h3>
      <p class="text-slate-700"><?= nl2br(htmlspecialchars($solicitud['observacion'])) ?></p>
    </div>
  <?php endif; ?>

  <!-- Lista de productos -->
  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <h3 class="font-bold text-slate-900 mb-4">📦 Productos solicitados</h3>
    
    <?php if (empty($productos)): ?>
      <p class="text-slate-500">No hay productos en esta solicitud.</p>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($productos as $p): ?>
          <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div>
              <h4 class="font-semibold text-slate-900"><?= htmlspecialchars($p['nom_producto']) ?></h4>
              <p class="text-sm text-slate-500">📦 <?= htmlspecialchars($p['categoria']) ?></p>
            </div>
            <div class="text-right">
              <p class="font-bold text-blue-600">
                <?= $p['cantidad_solicitada'] ?> <?= htmlspecialchars($p['unidad']) ?>
              </p>
              <p class="text-xs text-slate-500">
                Stock: <?= $p['cantidad_disponible'] ?> <?= htmlspecialchars($p['unidad']) ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Acciones para donante -->
  <?php if (isset($_SESSION['user']) && $_SESSION['user']['rol'] === 'donante' && $solicitud['estado'] === 'Pendiente'): ?>
    <div class="mt-8 flex gap-4">
      <a href="?controller=Solicitud&action=aprobar&id=<?= $solicitud['id_solicitud'] ?>"
         onclick="return confirm('¿Confirmar entrega de estos productos? Se descontará automáticamente del stock.')"
         class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-lg">
        ✓ Aprobar solicitud
      </a>

      <a href="?controller=Solicitud&action=rechazar&id=<?= $solicitud['id_solicitud'] ?>"
         onclick="return confirm('¿Rechazar esta solicitud?')"
         class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-lg">
        ✗ Rechazar solicitud
      </a>
    </div>
  <?php endif; ?>

</section>