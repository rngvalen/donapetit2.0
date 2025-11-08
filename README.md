## INFO PARA INSTALACION

### Requisitos
- Node.js 18 o superior (incluye npm). Descarga: https://nodejs.org/

### Instalacion automatica (Windows, macOS y Linux)
```bash
node install-deps.js
```

El script valida que `npm` este disponible y ejecuta `npm install`.

> Windows: sigue disponible `install-deps.bat` si prefieres doble clic.

### Instalacion manual
```bash
npm install
```

### Comandos utiles
- `npm run dev`: recompila los estilos de Tailwind en modo observacion.
- `npm run build`: genera los estilos optimizados para produccion.

### Configuracion de correo
- Copia el template `config/email.example.php` a `config/email.php` (este archivo ya está ignorado por Git) y completa los valores con tus credenciales reales.
- Opcionalmente podés definir las variables de entorno `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `MAIL_REPLY_TO` y `MAIL_ENCRYPTION` para no guardar secretos en disco.
- El servicio usa PHPMailer (incluido en `app/libs/PHPMailer`) para enviar el código de recuperación.

### Flujo de recuperacion de contrasena
1. `?controller=Auth&action=mostrarRecuperacion` pide el email del usuario y dispara el envio del codigo.
2. `?controller=Auth&action=mostrarVerificarCodigo` valida el codigo recibido (6 digitos, expira en 15 minutos).
3. `?controller=Auth&action=mostrarNuevaContrasena` permite definir la nueva contrasena.

Asegurate de ejecutar el script SQL actualizado (`donapetit.sql`) para crear la columna `codigo` en la tabla `codigo_verificacion`.
