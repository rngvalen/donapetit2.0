<?php
/**
 * Configuración de Email
 * Para desarrollo local con XAMPP, usar configuración de Gmail o Mailtrap
 */

return [
    'from_email' => 'donappetit59@gmail.com',
    'from_name' => 'DonAppetit',
    'reply_to' => '',

    'smtp' => [
        // Para desarrollo, puedes usar Mailtrap.io o Gmail
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'donappetit59@gmail.com', // Configura tu email
        'password' => 'svix knnn avvi vxkp', // Configura tu contraseña de aplicación de Gmail
        'encryption' => 'tls',
    ],
];
