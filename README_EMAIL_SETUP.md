# Configuración del Sistema de Recuperación de Contraseña por Email

Este documento describe cómo configurar el envío de emails para la recuperación de contraseña.

## Requisitos previos

1. **Composer instalado**: Necesitas tener Composer instalado en tu sistema.
2. **Servidor SMTP**: Credenciales de un servidor SMTP (Gmail, SendGrid, Mailtrap, etc.).

## Pasos de instalación

### 1. Instalar dependencias

```bash
cd c:\xampp\htdocs\donapetit3-main
composer install
```

Esto instalará PHPMailer y todas las dependencias necesarias.

### 2. Configurar credenciales SMTP

1. Copia el archivo de ejemplo:
   ```bash
   cp config/email.example.php config/email.php
   ```

2. Edita `config/email.php` con tus credenciales SMTP reales:

```php
return [
    'from_email' => 'noreply@tudominio.com',
    'from_name'  => 'DonAppetit',
    'reply_to'   => 'soporte@tudominio.com',
    'smtp' => [
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'username'   => 'tu-email@gmail.com',
        'password'   => 'tu-app-password',
        'encryption' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
    ],
];
```

### 3. Configurar Gmail (si usas Gmail)

Si usas Gmail, necesitas crear una "App Password":

1. Ve a tu cuenta de Google: https://myaccount.google.com/security
2. Activa la verificación en 2 pasos si no la tienes
3. Busca "App passwords" (Contraseñas de aplicaciones)
4. Genera una nueva contraseña para "Mail"
5. Usa esa contraseña de 16 caracteres en `config/email.php`

### 4. Alternativas a Gmail

#### Mailtrap (desarrollo/pruebas)
```php
'smtp' => [
    'host'       => 'smtp.mailtrap.io',
    'port'       => 2525,
    'username'   => 'tu-username-mailtrap',
    'password'   => 'tu-password-mailtrap',
    'encryption' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
]
```

#### SendGrid
```php
'smtp' => [
    'host'       => 'smtp.sendgrid.net',
    'port'       => 587,
    'username'   => 'apikey',
    'password'   => 'tu-api-key-sendgrid',
    'encryption' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
]
```

## Estructura de archivos creados

```
app/
├── controllers/
│   └── AuthController.php         (actualizado con EmailService)
├── model/
│   ├── authservice.php             (nuevo: genera y persiste códigos)
│   └── codigo_verificacion.php    (actualizado: métodos adicionales)
└── services/
    └── EmailService.php            (nuevo: envío de emails con PHPMailer)

config/
├── email.example.php               (plantilla de configuración)
└── email.php                       (configuración real - NO versionar)

composer.json                       (configuración de dependencias)
```

## Flujo de recuperación de contraseña

1. **Usuario solicita recuperación** (`?controller=Auth&action=mostrarRecuperacion`)
   - Ingresa su email

2. **Sistema genera código** (`AuthController::enviarCodigo()`)
   - Valida el email
   - Llama a `AuthService::requestPasswordReset()`
   - Genera código de 6 dígitos
   - Guarda código en BD con expiración de 15 minutos
   - Envía email con `EmailService::enviarCodigoRecuperacion()`

3. **Usuario verifica código** (`?controller=Auth&action=verificarCodigo`)
   - Ingresa el código recibido por email
   - Sistema valida que el código sea correcto y no haya expirado

4. **Usuario cambia contraseña** (`?controller=Auth&action=cambiarContrasena`)
   - Ingresa nueva contraseña
   - Sistema actualiza la contraseña
   - Desactiva el código usado

## Características implementadas

- Código de 6 dígitos con padding de ceros
- Expiración de 15 minutos
- Desactivación automática de códigos previos
- Email HTML responsive con diseño profesional
- Texto alternativo para clientes sin HTML
- Manejo de errores con mensajes comprensibles
- Validación de email antes de envío
- Sesión segura para enlazar email + código

## Seguridad

- El archivo `config/email.php` está en `.gitignore` (NO se versiona)
- Los códigos expiran automáticamente después de 15 minutos
- Se desactivan códigos previos al generar uno nuevo
- Los códigos se validan contra BD (activo=1 y no expirados)
- Las contraseñas se hashean con `password_hash()`

## Troubleshooting

### Error: "No pudimos enviar el email"
- Verifica que `config/email.php` existe y tiene credenciales correctas
- Revisa los logs de error de PHP
- Prueba las credenciales SMTP manualmente

### Error: "Archivo de configuración de email no encontrado"
- Asegúrate de copiar `config/email.example.php` a `config/email.php`

### Error: "Class 'PHPMailer' not found"
- Ejecuta `composer install` para instalar PHPMailer

### Email no llega
- Verifica spam/correo no deseado
- Comprueba que el servidor SMTP esté configurado correctamente
- Usa Mailtrap para pruebas en desarrollo

## Testing

Para probar el flujo completo:

1. Ve a `?controller=Auth&action=mostrarRecuperacion`
2. Ingresa un email registrado
3. Revisa tu bandeja de entrada (o Mailtrap si estás en desarrollo)
4. Ingresa el código recibido
5. Cambia tu contraseña
6. Inicia sesión con la nueva contraseña

## Notas importantes

- **NO subas `config/email.php` a git** (contiene credenciales sensibles)
- En producción, usa variables de entorno para las credenciales
- Considera implementar rate limiting para prevenir spam
- Los códigos se guardan en la tabla `codigo_verificacion`
