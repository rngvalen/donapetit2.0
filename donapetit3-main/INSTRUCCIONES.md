# 🚀 Instrucciones para iniciar DonAppetit

## ✅ Todo está configurado correctamente

Ya se instalaron todas las dependencias necesarias. Solo necesitas iniciar los servicios de XAMPP.

## 📋 Pasos para iniciar el proyecto

### 1. Iniciar XAMPP
1. Abre el **Panel de Control de XAMPP**
2. Inicia el servicio **Apache** (botón Start)
3. Inicia el servicio **MySQL** (botón Start)

### 2. Verificar la instalación
Abre tu navegador y visita:
```
http://localhost/donapetit2.0/donapetit3-main/test_config.php
```

Este script verificará:
- ✓ Versión de PHP
- ✓ PHPMailer instalado
- ✓ Conexión a base de datos
- ✓ Extensiones PHP necesarias
- ✓ Archivos de configuración
- ✓ Tabla verificar_contrasena

### 3. Acceder al proyecto
Una vez que todos los tests sean exitosos, accede al proyecto:
```
http://localhost/donapetit2.0/donapetit3-main/
```

## 📧 Configuración de Email (ya está lista)

El sistema de recuperación de contraseña por email está configurado con:
- **SMTP:** Gmail
- **Email:** donappetit59@gmail.com
- **Encriptación:** TLS
- **Puerto:** 587

## 🔑 Funcionalidades implementadas

### Sistema de autenticación
- ✅ Login
- ✅ Registro de usuarios (Donante/Receptor)
- ✅ Recuperación de contraseña por email
- ✅ Código de verificación de 6 dígitos (15 minutos de expiración)

### Rutas disponibles
- **Login:** `?controller=Auth&action=mostrarLogin`
- **Registro:** `?controller=Auth&action=mostrarRegistro`
- **Recuperar contraseña:** `?controller=Auth&action=mostrarRecuperacion`
- **Home:** `?controller=Home&action=index`

## 🗄️ Base de datos

**Nombre:** donappetit
**Host:** localhost
**Usuario:** root
**Contraseña:** (vacía)

### Tablas importantes
- `usuarios` - Usuarios del sistema
- `donante` - Datos de donantes
- `receptor` - Datos de receptores
- `verificar_contrasena` - Códigos de recuperación
- `solicitud` - Solicitudes de donación
- `productos_donante` - Productos disponibles

## 🛠️ Dependencias instaladas

- **PHPMailer 6.12.0** - Para envío de emails
- **Composer** - Gestor de dependencias PHP

## ⚠️ Problemas comunes

### Si Apache no inicia:
- Verifica que el puerto 80 no esté ocupado
- Cierra Skype u otros programas que usen el puerto 80

### Si MySQL no inicia:
- Verifica que el puerto 3306 no esté ocupado
- Asegúrate de que no haya otra instancia de MySQL corriendo

### Si no funciona el envío de emails:
- Verifica que la contraseña de aplicación de Gmail sea correcta
- Revisa la configuración en `config/email.php`

## 📁 Estructura del proyecto

```
donapetit3-main/
├── app/
│   ├── controllers/     # Controladores
│   ├── model/          # Modelos
│   ├── view/           # Vistas
│   ├── services/       # Servicios (EmailService)
│   └── core/           # Router y núcleo
├── config/             # Configuraciones
│   ├── bdconexion.php  # Conexión a BD
│   └── email.php       # Config de email
├── public/             # Punto de entrada
├── vendor/             # Dependencias de Composer
├── composer.json       # Configuración de Composer
└── test_config.php     # Script de verificación

```

## 🎯 Próximos pasos

1. Inicia XAMPP (Apache + MySQL)
2. Visita el test de configuración
3. Si todo está en verde, ¡ya puedes usar el proyecto!

---

**¡El proyecto está listo para funcionar! 🎉**

Si tienes algún problema, revisa el archivo `test_config.php` para ver qué está fallando.
