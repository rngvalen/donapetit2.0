<?php
$solicitudes = $solicitudes ?? [];
$titulo = $titulo ?? 'Mis solicitudes';
?>
<section class="max-w-7xl mx-auto px-4 py-8">
  
  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900"><?= htmlspecialchars($titulo) ?></h1>
    <p class="mt-2 text-slate-600">Historial de solicitudes realizadas</p>
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
      <p class="text-slate-500 text-lg">No tienes solicitudes realizadas.</p>
      <a href="?controller=Producto&action=productosDisponibles"
         class="mt-4 inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition">
        Ver productos disponibles
      </a>
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

        $isAprobadaSinConfirmar = $s['estado'] === 'Aprobada' && isset($s['retiro_confirmado']) && $s['retiro_confirmado'] == 0;
        $isAprobadaConfirmada = $s['estado'] === 'Aprobada' && isset($s['retiro_confirmado']) && $s['retiro_confirmado'] == 1;

        // Calcular tiempo restante para retirar
        $tiempoRestante = null;
        if ($isAprobadaSinConfirmar && isset($s['fecha_limite_retiro'])) {
            $ahora = time();
            $limite = strtotime($s['fecha_limite_retiro']);
            $diferencia = $limite - $ahora;
            if ($diferencia > 0) {
                $horas = floor($diferencia / 3600);
                $minutos = floor(($diferencia % 3600) / 60);
                $tiempoRestante = $horas > 0 ? "{$horas}h {$minutos}m" : "{$minutos}m";
            } else {
                $tiempoRestante = "Expirado";
            }
        }
        ?>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-xl transition">
          
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-bold text-slate-900 text-lg">
                Solicitud #<?= $s['id_solicitud'] ?>
              </h3>
              <p class="text-sm text-slate-500 mt-1">
                🏪 <?= htmlspecialchars($s['nombre_donante'] ?? 'Donante') ?>
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

            <?php if ($isAprobadaSinConfirmar && $tiempoRestante): ?>
              <div class="mt-3 p-4 bg-blue-50 border-2 border-blue-400 rounded-lg">
                <p class="font-bold text-blue-900 text-base">✓ ¡Tu solicitud fue aprobada!</p>
                <p class="text-sm text-blue-800 mt-1">
                  ⏰ Tienes <strong><?= $tiempoRestante ?></strong> para retirar los productos
                </p>
                <p class="text-xs text-blue-600 mt-1">
                  Fecha límite: <?= date('d/m/Y H:i', strtotime($s['fecha_limite_retiro'])) ?>
                </p>
              </div>
            <?php endif; ?>

            <?php if ($isAprobadaConfirmada && isset($s['fecha_retiro'])): ?>
              <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="font-bold text-green-900">✓ Retiro completado</p>
                <p class="text-xs text-green-700 mt-1">
                  Fecha: <?= date('d/m/Y H:i', strtotime($s['fecha_retiro'])) ?>
                </p>
              </div>
            <?php endif; ?>
          </div>

          <div class="pt-4 border-t border-slate-200 flex gap-2">
            <a href="?controller=Solicitud&action=ver&id=<?= $s['id_solicitud'] ?>"
               class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition text-sm">
              Ver detalle
            </a>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</section>