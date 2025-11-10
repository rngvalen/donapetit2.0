<?php
$solicitudes = $solicitudes ?? [];
$titulo = $titulo ?? 'Solicitudes recibidas';
?>
<section class="max-w-7xl mx-auto px-4 py-8">
  
  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900"><?= htmlspecialchars($titulo) ?></h1>
    <p class="mt-2 text-slate-600">Solicitudes de receptores que quieren tus productos</p>
  </header>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 rounded-xl border border-green-300 bg-green-50 text-green-800 text-sm p-4">
      <p class="font-semibold">✅ <?= htmlspecialchars($_SESSION['success']) ?></p>
      <?php unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 rounded-xl border border-red-300 bg-red-50 text-red-800 text-sm p-4">
      <p class="font-semibold">❌ <?= htmlspecialchars($_SESSION['error']) ?></p>
      <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <?php if (empty($solicitudes)): ?>
    <div class="text-center py-16 bg-slate-50 rounded-2xl">
      <p class="text-slate-500 text-lg">No tienes solicitudes pendientes.</p>
    </div>
  <?php else: ?>

    <div class="grid grid-cols-1 gap-6">
      <?php foreach ($solicitudes as $s): ?>
        <?php
        // Determinar color según estado
        $estadoClasses = [
            'Pendiente' => 'bg-yellow-100 text-yellow-800',
            'Aprobada' => 'bg-green-100 text-green-800',
            'Rechazada' => 'bg-red-100 text-red-800'
        ];
        $estadoClass = $estadoClasses[$s['estado']] ?? 'bg-slate-100 text-slate-800';
        ?>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-xl transition">
          
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-bold text-slate-900 text-lg">
                Solicitud #<?= $s['id_solicitud'] ?>
              </h3>
              <p class="text-sm text-slate-500 mt-1">
                🏢 <?= htmlspecialchars($s['nom_institucion']) ?>
              </p>
              <p class="text-xs text-slate-400 mt-1">
                👤 <?= htmlspecialchars($s['nombre_receptor']) ?> • 
                📧 <?= htmlspecialchars($s['email_receptor']) ?>
              </p>
              <p class="text-xs text-slate-400 mt-1">
                📅 <?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?>
              </p>
            </div>
            
            <span class="px-4 py-2 <?= $estadoClass ?> text-sm font-semibold rounded-full">
              <?= htmlspecialchars($s['estado']) ?>
            </span>
          </div>

          <div class="space-y-2 text-sm text-slate-600 mb-4">
            <p><strong>Productos solicitados:</strong> <?= $s['total_productos'] ?></p>
            <?php if ($s['observacion']): ?>
              <p><strong>Observaciones:</strong> <?= htmlspecialchars($s['observacion']) ?></p>
            <?php endif; ?>
          </div>

          <div class="pt-4 border-t border-slate-200 flex gap-2">
            <a href="?controller=Solicitud&action=ver&id=<?= $s['id_solicitud'] ?>"
               class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition text-sm">
              Ver detalle
            </a>

            <?php if ($s['estado'] === 'Pendiente'): ?>
              <a href="?controller=Solicitud&action=aprobar&id=<?= $s['id_solicitud'] ?>"
                 onclick="return confirm('¿Confirmar entrega de estos productos? Se descontará automáticamente del stock.')"
                 class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-medium rounded-lg transition text-sm">
                ✓ Aprobar
              </a>

              <a href="?controller=Solicitud&action=rechazar&id=<?= $s['id_solicitud'] ?>"
                 onclick="return confirm('¿Rechazar esta solicitud?')"
                 class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition text-sm">
                ✗ Rechazar
              </a>
            <?php endif; ?>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</section>