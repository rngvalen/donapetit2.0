<?php
/**
 * Configuración de Email / SMTP
 *
 * Copia este archivo a config/email.php y configura tus credenciales SMTP.
 * NO subas config/email.php a tu repositorio (debe estar en .gitignore).
 */

return [
    // Remitente del email
    'from_email' => 'noreply@donappetit.com',
    'from_name'  => 'DonAppetit',

    // Email de respuesta (opcional)
    'reply_to'   => 'soporte@donappetit.com',

    // Configuración SMTP
    'smtp' => [
        'host'       => 'smtp.gmail.com',         // Servidor SMTP (ej: smtp.gmail.com, smtp.mailtrap.io)
        'port'       => 587,                       // Puerto (587 para TLS, 465 para SSL)
        'username'   => 'tu-email@gmail.com',     // Usuario SMTP
        'password'   => 'tu-contraseña-app',      // Contraseña o App Password
        'encryption' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS, // TLS o SSL
    ],
];
