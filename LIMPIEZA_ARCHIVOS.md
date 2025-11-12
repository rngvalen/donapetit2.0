# Limpieza de Archivos No Utilizados

Fecha: 2025-11-12

## Archivos y Carpetas Eliminados

### Modelos Eliminados (`app/model/`)
- ✅ **estadistica.php** - No se estaba usando, reemplazado por donacion.php
- ✅ **Usuario.php** - No se estaba usando directamente (se usa la tabla usuarios)
- ✅ **direcciones.php** - No se estaba usando (direcciones se manejan inline en MapController)

### Controladores Eliminados (`app/controllers/`)
- ✅ **EstadisticaController.php** - Archivo vacío sin implementación
- ✅ **ConfirmController.php** - No se estaba llamando desde ninguna vista
- ✅ **AdminController.php** - Panel de administración no implementado

### Vistas Eliminadas (`app/view/`)
- ✅ **admin/** - Carpeta completa con vistas del panel de administración no usado
  - admin_principal.php
  - catalog_snapshot.php
  - catalogo_de_productos.php
  - notification_settings.php
  - user_management.php
- ✅ **confirm/** - Carpeta con vista de confirmación no usada
  - order_confirmation.php
- ✅ **ussers/** - Carpeta vacía (solo contenía index.php vacío)

### Carpetas de Sistema Eliminadas
- ✅ **.history/** - Carpeta completa con historial de cambios de VSCode (>100 archivos)

## Archivos y Carpetas que se MANTIENEN

### Modelos en Uso (`app/model/`)
- ✅ **Auth.php** - Autenticación de usuarios
- ✅ **authservice.php** - Servicio de recuperación de contraseña
- ✅ **Catalogo.php** - Catálogo de productos
- ✅ **Categoria.php** - Categorías de productos
- ✅ **codigo_verificacion.php** - Códigos de verificación para reset de contraseña
- ✅ **donacion.php** - Estadísticas de donaciones
- ✅ **donante.php** - Perfil de donantes
- ✅ **model.php** - Clase base para todos los modelos
- ✅ **Producto.php** - Productos del catálogo
- ✅ **ProductoDonante.php** - Productos publicados por donantes
- ✅ **receptor.php** - Perfil de receptores
- ✅ **Solicitud.php** - Sistema de solicitudes y reservas
- ✅ **Unidad.php** - Unidades de medida

### Controladores en Uso (`app/controllers/`)
- ✅ **AuthController.php** - Login, registro, recuperación de contraseña
- ✅ **controller.php** - Clase base para controladores
- ✅ **HomeController.php** - Página de inicio y estadísticas
- ✅ **infocontroller.php** - Páginas de información (usado en footer)
- ✅ **MapController.php** - Mapa de donantes
- ✅ **ProductoController.php** - CRUD de productos
- ✅ **ProfileController.php** - Perfiles de usuario
- ✅ **SolicitudController.php** - Gestión de solicitudes

### Vistas en Uso (`app/view/`)
- ✅ **auth/** - Vistas de autenticación (login, registro, recuperación)
- ✅ **home/** - Dashboard de donantes y receptores
- ✅ **info/** - Páginas de información (contacto, ayuda, privacidad)
- ✅ **layouts/** - Plantillas (header, footer)
- ✅ **map/** - Mapa interactivo
- ✅ **products/** - CRUD de productos
- ✅ **profile/** - Formularios de perfil
- ✅ **solicitudes/** - Gestión de solicitudes
- ✅ **statics/** - Dashboard de estadísticas

## Resumen

### Archivos Eliminados
- **Modelos:** 3 archivos
- **Controladores:** 3 archivos
- **Vistas:** 7 archivos + 3 carpetas completas
- **Sistema:** 1 carpeta (.history con >100 archivos)

### Total Aproximado
**~120 archivos eliminados** (incluyendo .history)

### Espacio Liberado
Aproximadamente **2-5 MB** de código no utilizado

### Beneficios
- ✅ Código más limpio y mantenible
- ✅ Menor confusión sobre qué archivos están en uso
- ✅ Más rápido para navegar el proyecto
- ✅ Sin archivos obsoletos o vacíos
- ✅ Estructura más clara del proyecto

## Verificación Post-Limpieza

Todos los archivos eliminados fueron verificados para asegurar que:
1. No se estaban usando en ningún controlador
2. No se estaban importando con `require_once`
3. No se estaban referenciando en las vistas
4. No afectan el funcionamiento del sistema

El sistema sigue 100% funcional después de la limpieza.
