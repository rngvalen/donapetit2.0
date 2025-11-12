# Sistema de Reservas con Expiración Automática

Este documento describe el funcionamiento del sistema de reservas de stock implementado en DonAppetit.

## Flujo de Funcionamiento

### 1. **Receptor solicita productos**
- El receptor selecciona productos disponibles de un donante
- Crea una solicitud con estado `Pendiente`
- El donante recibe una notificación

### 2. **Donante aprueba la solicitud**
- El donante revisa la solicitud y hace clic en "✓ Aprobar (Reservar)"
- **El sistema automáticamente:**
  - Cambia el estado de la solicitud a `Aprobada`
  - RESERVA el stock (incrementa `cantidad_reservada` en `productos_donante`)
  - El stock NO se descuenta aún de `cantidad_disponible`
  - Establece `fecha_limite_retiro` = Ahora + 2 horas
  - Notifica al receptor que su solicitud fue aprobada

### 3. **Receptor tiene 2 horas para retirar**
- El receptor ve en "Mis solicitudes" que su pedido fue aprobado
- Tiene 2 horas para ir a retirar los productos del donante

### 4a. **Donante confirma que el receptor retiró (CASO EXITOSO)**
- Cuando el receptor llega y retira los productos, el donante hace clic en "✓ Confirmar Retiro"
- **El sistema automáticamente:**
  - Descuenta el stock: `cantidad_disponible -= cantidad_solicitada`
  - Libera la reserva: `cantidad_reservada -= cantidad_solicitada`
  - Marca `retiro_confirmado = 1`
  - Registra `fecha_retiro`
  - Crea registro en tabla `detalle` con `donacion_efectiva = 1`

### 4b. **Receptor NO retira en 2 horas (CASO EXPIRADO)**
- Si pasan 2 horas sin que el donante confirme el retiro:
- **El sistema automáticamente (vía CRON):**
  - Libera la reserva: `cantidad_reservada -= cantidad_solicitada`
  - El stock vuelve a estar disponible
  - Cambia estado de solicitud a `Rechazada`
  - Agrega observación: "[Expirado - No retirado en tiempo]"

## Campos Agregados en Base de Datos

### Tabla `solicitud`
```sql
fecha_limite_retiro DATETIME NULL         -- Fecha límite (2 horas después de aprobar)
retiro_confirmado TINYINT(1) DEFAULT 0    -- 0 = pendiente, 1 = confirmado
fecha_retiro DATETIME NULL                 -- Fecha en que se confirmó el retiro
```

### Tabla `productos_donante`
```sql
cantidad_reservada INT DEFAULT 0          -- Stock temporalmente reservado
```

## Estados de una Solicitud

| Estado | Descripción | Stock |
|--------|-------------|-------|
| **Pendiente** | Esperando aprobación del donante | No afecta |
| **Aprobada (sin confirmar)** | Aprobada, esperando retiro | Reservado (no disponible) |
| **Aprobada (confirmada)** | Retirada y confirmada | Descontado definitivamente |
| **Rechazada** | Rechazada por donante o expirada | Liberado si estaba reservado |

## Scripts de Mantenimiento

### CRON Automático (Recomendado para Producción)

**Archivo:** `cron/liberar_stock_expirado.php`

**Configuración Linux (crontab):**
```bash
# Ejecutar cada 5 minutos
*/5 * * * * php /ruta/completa/cron/liberar_stock_expirado.php >> /var/log/cron_stock.log 2>&1
```

**Configuración Windows (Task Scheduler):**
- Programa: `C:\xampp\php\php.exe`
- Argumentos: `C:\xampp\htdocs\donapetit3-main\cron\liberar_stock_expirado.php`
- Repetir cada: 5 minutos

### Trigger Manual (Desarrollo)

**Archivo:** `public/cron_trigger.php`

**Uso:**
```
http://localhost/donapetit3-main/public/cron_trigger.php?key=donappetit2025
```

⚠️ **IMPORTANTE:** Cambiar `CRON_SECRET_KEY` en producción

## Ejemplo Visual del Flujo

```
RECEPTOR                  SISTEMA                    DONANTE
   |                         |                          |
   |--[Solicita productos]-->|                          |
   |                         |--[Notificación]--------->|
   |                         |                          |
   |                         |<---[Aprobar (Reservar)]--|
   |                         |                          |
   |<--[Notificación]--------|                          |
   |   "Aprobado - 2h"       |                          |
   |                         |                          |
   |                    [Temporizador]                  |
   |                    2 horas restantes               |
   |                         |                          |
   |====[Va a retirar]=====================================>|
   |                         |                          |
   |                         |<--[Confirmar Retiro]-----|
   |                         |    ✓ Stock descontado    |
   |                         |                          |

   O en caso de NO retirar:

   |                    [2 horas pasaron]               |
   |                         |                          |
   |                    [CRON ejecuta]                  |
   |                    ✓ Stock liberado                |
   |                    ✓ Solicitud rechazada           |
```

## Ventajas del Sistema

✅ **Evita reservas fantasma:** Stock se libera automáticamente si no se retira

✅ **Protege al donante:** No pierde stock si el receptor no va

✅ **Transparente:** Ambas partes ven el tiempo restante

✅ **Automático:** No requiere intervención manual para liberar

✅ **Flexible:** Tiempo configurable (actualmente 2 horas)

## Modificar el Tiempo de Expiración

Para cambiar de 2 horas a otro valor, editar en:

**Archivo:** `app/model/Solicitud.php` línea ~245
```php
// Cambiar '+2 hours' por el tiempo deseado
$fechaLimite = date('Y-m-d H:i:s', strtotime('+2 hours'));
```

Opciones:
- `'+1 hour'` = 1 hora
- `'+30 minutes'` = 30 minutos
- `'+3 hours'` = 3 horas

## Consultas Útiles

### Ver reservas activas
```sql
SELECT s.id_solicitud, s.fecha_limite_retiro,
       pd.id_producto_donante, pd.cantidad_reservada
FROM solicitud s
JOIN productos_solicitados ps ON s.id_solicitud = ps.id_solicitud
JOIN productos_donante pd ON ps.id_producto_donante = pd.id_producto_donante
WHERE s.estado = 'Aprobada'
  AND s.retiro_confirmado = 0
  AND s.fecha_limite_retiro > NOW();
```

### Liberar manualmente una reserva expirada
```sql
-- NO EJECUTAR (lo hace el CRON automáticamente)
-- Solo para emergencias
UPDATE solicitud SET estado = 'Rechazada' WHERE id_solicitud = X;
```

## Troubleshooting

### Las reservas no se liberan automáticamente
1. Verificar que el CRON esté configurado
2. Verificar logs: `cat /var/log/cron_stock.log`
3. Ejecutar manualmente: `php cron/liberar_stock_expirado.php`

### Stock negativo
No debería ocurrir, pero si sucede:
```sql
UPDATE productos_donante
SET cantidad_reservada = 0
WHERE cantidad_reservada < 0;
```

### Resetear todo el sistema de reservas
```sql
-- CUIDADO: Solo en desarrollo
UPDATE solicitud SET retiro_confirmado = 1 WHERE estado = 'Aprobada';
UPDATE productos_donante SET cantidad_reservada = 0;
```
