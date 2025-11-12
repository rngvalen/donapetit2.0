<?php
$solicitudes = $solicitudes ?? [];
$titulo = $titulo ?? 'Solicitudes recibidas';

// Contar pendientes
$pendientes = array_filter($solicitudes, function($s) {
    return $s['estado'] === 'Pendiente';
});
$totalPendientes = count($pendientes);
?>
<section class="max-w-7xl mx-auto px-4 py-8">
  
  <header class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900"><?= htmlspecialchars($titulo) ?></h1>
    <p class="mt-2 text-slate-600">Solicitudes de receptores que quieren tus productos</p>
  </header>

  <!-- Banner de pendientes -->
  <?php if ($totalPendientes > 0): ?>
    <div class="mb-6 rounded-xl border-2 border-amber-400 bg-amber-50 p-6">
      <div class="flex items-center gap-4">
        <div class="flex-shrink-0">
          <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
        </div>
        <div class="flex-1">
          <h3 class="text-lg font-bold text-amber-900">
            <?= $totalPendientes ?> solicitud<?= $totalPendientes > 1 ? 'es' : '' ?> pendiente<?= $totalPendientes > 1 ? 's' : '' ?>
          </h3>
          <p class="text-sm text-amber-700 mt-1">
            Tienes solicitudes esperando tu respuesta. Revísalas y aprueba las que puedas cumplir.
          </p>
        </div>
      </div>
    </div>
  <?php endif; ?>

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
            'Pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'Aprobada' => 'bg-green-100 text-green-800 border-green-300',
            'Rechazada' => 'bg-red-100 text-red-800 border-red-300'
        ];
        $estadoClass = $estadoClasses[$s['estado']] ?? 'bg-slate-100 text-slate-800';
        $isPendiente = $s['estado'] === 'Pendiente';
        $isAprobada = $s['estado'] === 'Aprobada';
        $retiroConfirmado = isset($s['retiro_confirmado']) && $s['retiro_confirmado'] == 1;

        // Calcular tiempo restante si está aprobada pero no retirada
        $tiempoRestante = null;
        $expirada = false;
        if ($isAprobada && !$retiroConfirmado && isset($s['fecha_limite_retiro'])) {
            $ahora = new DateTime();
            $limite = new DateTime($s['fecha_limite_retiro']);
            if ($ahora < $limite) {
                $diff = $ahora->diff($limite);
                $tiempoRestante = $diff->format('%h:%I:%S');
            } else {
                $expirada = true;
            }
        }

        $cardClass = $isPendiente
          ? 'bg-white border-2 border-amber-300 shadow-amber-100'
          : ($isAprobada && !$retiroConfirmado ? 'bg-white border-2 border-blue-300 shadow-blue-100' : 'bg-white border border-slate-200');
        ?>
        
        <div class="<?= $cardClass ?> rounded-2xl p-6 hover:shadow-xl transition">
          
          <div class="flex items-start justify-between mb-4">
            <div>
              <div class="flex items-center gap-2">
                <h3 class="font-bold text-slate-900 text-lg">
                  Solicitud #<?= $s['id_solicitud'] ?>
                </h3>
                <?php if ($isPendiente): ?>
                  <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded-full animate-pulse">
                    NUEVA
                  </span>
                <?php endif; ?>
              </div>
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
            
            <span class="px-4 py-2 <?= $estadoClass ?> text-sm font-semibold rounded-full border-2">
              <?= htmlspecialchars($s['estado']) ?>
            </span>
          </div>

          <div class="space-y-2 text-sm text-slate-600 mb-4">
            <p><strong>Productos solicitados:</strong> <?= $s['total_productos'] ?></p>
            <?php if ($s['observacion']): ?>
              <p><strong>Observaciones:</strong> <?= htmlspecialchars($s['observacion']) ?></p>
            <?php endif; ?>

            <?php if ($isAprobada && !$retiroConfirmado): ?>
              <?php if ($tiempoRestante): ?>
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                  <p class="text-blue-800 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    ⏰ Tiempo restante: <span class="font-mono"><?= $tiempoRestante ?></span>
                  </p>
                  <p class="text-xs text-blue-600 mt-1">Stock reservado hasta: <?= date('d/m/Y H:i', strtotime($s['fecha_limite_retiro'])) ?></p>
                </div>
              <?php elseif ($expirada): ?>
                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                  <p class="text-red-800 font-semibold">⚠️ Reserva expirada - Se liberará automáticamente</p>
                </div>
              <?php endif; ?>
            <?php elseif ($isAprobada && $retiroConfirmado): ?>
              <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800 font-semibold">✅ Retiro confirmado el <?= date('d/m/Y H:i', strtotime($s['fecha_retiro'])) ?></p>
              </div>
            <?php endif; ?>
          </div>

          <div class="pt-4 border-t border-slate-200 flex gap-2">
            <a href="?controller=Solicitud&action=ver&id=<?= $s['id_solicitud'] ?>"
               class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition text-sm">
              Ver detalle
            </a>

            <?php if ($s['estado'] === 'Pendiente'): ?>
              <a href="?controller=Solicitud&action=aprobar&id=<?= $s['id_solicitud'] ?>"
                 onclick="return confirm('¿Aprobar esta solicitud? Se reservará el stock por 2 horas.')"
                 class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition text-sm shadow-lg">
                ✓ Aprobar (Reservar)
              </a>

              <a href="?controller=Solicitud&action=rechazar&id=<?= $s['id_solicitud'] ?>"
                 onclick="return confirm('¿Rechazar esta solicitud?')"
                 class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition text-sm">
                ✗ Rechazar
              </a>
            <?php elseif ($isAprobada && !$retiroConfirmado && !$expirada): ?>
              <a href="?controller=Solicitud&action=confirmarRetiro&id=<?= $s['id_solicitud'] ?>"
                 onclick="return confirm('¿Confirmar que el receptor retiró los productos? Se descontará el stock definitivamente.')"
                 class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm shadow-lg">
                ✓ Confirmar Retiro
              </a>
            <?php endif; ?>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</section>