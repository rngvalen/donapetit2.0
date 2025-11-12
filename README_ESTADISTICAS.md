# Sistema de Estadísticas de Donaciones

Este documento describe el funcionamiento del sistema de estadísticas implementado en DonAppétit.

## Características

El sistema de estadísticas permite a los **donantes** visualizar el impacto de sus donaciones a través de métricas y gráficos interactivos.

### Métricas Disponibles

1. **Total Donaciones**: Cantidad total de donaciones efectivas realizadas por el donante
2. **Alimentos Salvados**: Total de unidades de alimentos donadas
3. **Productos más Frecuentes**: Gráfico de barras horizontal mostrando los 5 productos más donados
4. **Frecuencia Mensual**: Gráfico de línea mostrando la evolución de donaciones en los últimos 6 meses

## Restricciones de Acceso

- ✅ **Solo donantes** pueden acceder a las estadísticas
- ❌ Los receptores **no tienen acceso** a esta funcionalidad
- 🔒 Las estadísticas están **filtradas por donante** (cada donante solo ve sus propios datos)

## Arquitectura

### Modelo de Datos

**Archivo:** [app/model/donacion.php](app/model/donacion.php)

El modelo `Donacion` trabaja con la tabla `detalle` que registra donaciones efectivas (confirmadas).

#### Métodos Principales

```php
// Cuenta total de donaciones efectivas del donante
public function totalRegistros(?int $idDonante = null): int

// Suma total de unidades donadas
public function totalCantidad(?int $idDonante = null): int

// Top 5 productos más donados (gráfico de barras)
public function topProductos(int $limit = 5, ?int $idDonante = null): array

// Donaciones agrupadas por mes (gráfico de línea)
public function frecuenciaMensual(int $months = 6, ?int $idDonante = null): array
```

**Nota:** Todos los métodos aceptan un parámetro opcional `$idDonante` para filtrar resultados.

### Consultas SQL

#### Total de Donaciones
```sql
SELECT COUNT(*)
FROM detalle d
INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
WHERE d.donacion_efectiva = 1
AND pd.id_donante = ?
```

#### Productos Más Donados
```sql
SELECT cat.nom_producto, SUM(d.cantidad_donada) AS total
FROM detalle d
INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
INNER JOIN catalogo_productos cat ON cat.id_catalogo = pd.id_catalogo
WHERE d.donacion_efectiva = 1
AND pd.id_donante = ?
GROUP BY cat.nom_producto
ORDER BY total DESC
LIMIT 5
```

#### Frecuencia Mensual
```sql
SELECT DATE_FORMAT(d.fecha_registro, '%Y-%m') AS month_key,
       SUM(d.cantidad_donada) AS total
FROM detalle d
INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
WHERE d.donacion_efectiva = 1
AND d.fecha_registro >= :start
AND pd.id_donante = :id_donante
GROUP BY month_key
ORDER BY month_key
```

### Controlador

**Archivo:** [app/controllers/HomeController.php](app/controllers/HomeController.php)

```php
public function statics(): void
{
    // Verifica autenticación
    if (!isset($_SESSION['user'])) {
        header('Location: ?controller=Auth&action=mostrarLogin');
        exit;
    }

    // Solo donantes pueden ver estadísticas
    if ($_SESSION['user']['rol'] !== 'donante') {
        $_SESSION['error'] = 'Solo los donantes pueden ver estadísticas.';
        header('Location: ?controller=Home&action=index');
        exit;
    }

    // Obtiene estadísticas filtradas por el donante actual
    $userId = (int)$_SESSION['user']['id'];
    $stats = $this->buildStatisticsContext($userId);

    $this->render('statics.statics_main', $stats);
}
```

### Vista

**Archivo:** [app/view/statics/statics_main.php](app/view/statics/statics_main.php)

La vista utiliza **Chart.js** para renderizar gráficos interactivos:

- **Gráfico de Barras Horizontal**: Productos más donados
- **Gráfico de Línea**: Evolución mensual de donaciones

**CDN Utilizado:**
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

## Flujo de Usuario

1. **Donante accede al dashboard desde el menú principal**
   - Ruta: `index.php?controller=Home&action=statics`
   - Card disponible en la vista de inicio del donante

2. **Sistema verifica permisos**
   - ✅ Si es donante → Muestra estadísticas filtradas
   - ❌ Si es receptor → Redirige con mensaje de error

3. **Se cargan datos del donante**
   - El sistema obtiene el `id_usuario` de la sesión
   - Ejecuta queries filtrando por `pd.id_donante = ?`

4. **Se renderizan gráficos**
   - Chart.js procesa los datos en formato JSON
   - Muestra visualizaciones interactivas

## Acceso Rápido

### Para Donantes
Desde la vista principal (`home/index.php`), aparece una card:

```
┌─────────────────────────────────┐
│ 📊 Estadísticas                  │
│                                  │
│ Consulta métricas sobre tus     │
│ donaciones, productos más        │
│ donados y frecuencia mensual.    │
│                                  │
│ [Ver dashboard]                  │
└─────────────────────────────────┘
```

### URL Directa
```
http://localhost/donapetit3-main/index.php?controller=Home&action=statics
```

## Datos Considerados

Solo se contabilizan donaciones con:
- `donacion_efectiva = 1` en tabla `detalle`
- Productos asociados al donante actual (`pd.id_donante`)
- Retiros confirmados (stock efectivamente entregado)

## Personalización

### Cambiar cantidad de productos en el top
Editar en [HomeController.php:117](app/controllers/HomeController.php#L117):
```php
$topProductos = $donacionModel->topProductos(5, $idDonante); // Cambiar 5 por el valor deseado
```

### Cambiar cantidad de meses en frecuencia
Editar en [HomeController.php:118](app/controllers/HomeController.php#L118):
```php
$frecuenciaMensual = $donacionModel->frecuenciaMensual(6, $idDonante); // Cambiar 6 por el valor deseado
```

### Cambiar colores de gráficos
Editar en [statics_main.php:93](app/view/statics/statics_main.php#L93):
```javascript
var baseColors = ['#3d538f', '#212E50', '#161D30', '#2F3953', '#1B2B55'];
```

## Troubleshooting

### No aparecen datos en los gráficos
1. Verificar que existan registros en tabla `detalle` con `donacion_efectiva = 1`
2. Verificar que los productos estén asociados al donante actual
3. Verificar que `fecha_registro` esté dentro del rango de los últimos 6 meses

```sql
-- Verificar donaciones efectivas del donante
SELECT COUNT(*)
FROM detalle d
INNER JOIN productos_solicitados ps ON ps.id_producto_solicitado = d.id_producto_solicitado
INNER JOIN productos_donante pd ON pd.id_producto_donante = ps.id_producto_donante
WHERE d.donacion_efectiva = 1
AND pd.id_donante = [ID_DONANTE];
```

### Error "Solo los donantes pueden ver estadísticas"
El usuario está autenticado como receptor. Las estadísticas solo están disponibles para usuarios con `rol = 'donante'`.

### Gráficos no se cargan
Verificar que Chart.js esté cargando correctamente desde el CDN. Revisar la consola del navegador para errores.

## Mantenimiento

### Agregar nuevas métricas
1. Agregar método en [app/model/donacion.php](app/model/donacion.php)
2. Llamar al método en `buildStatisticsContext()` del controlador
3. Pasar la variable a la vista
4. Renderizar en [app/view/statics/statics_main.php](app/view/statics/statics_main.php)

### Agregar nuevos gráficos
1. Agregar `<canvas>` en la vista
2. Preparar datos en formato JSON desde PHP
3. Crear instancia de Chart.js con configuración deseada

## Seguridad

✅ **Implementado:**
- Verificación de autenticación (`$_SESSION['user']`)
- Verificación de rol (solo donantes)
- Filtrado por ID de donante (cada donante solo ve sus datos)
- Uso de prepared statements (prevención de SQL injection)

## Futuras Mejoras

- [ ] Exportar estadísticas a PDF
- [ ] Comparativa con meses anteriores
- [ ] Gráfico de impacto social (receptores beneficiados)
- [ ] Ranking de productos más donados en la plataforma
- [ ] Métricas de puntualidad de retiros
