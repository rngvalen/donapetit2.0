<?php
return [
    'from_email' => 'noreply@example.com',
    'from_name'  => 'DonAppetit',
    'reply_to'   => null,
    'smtp' => [
        'host'       => 'smtp.mailtrap.io',
        'port'       => 587,
        'username'   => 'your-smtp-user',
        'password'   => 'your-smtp-password',
        'encryption' => 'tls', // tls | ssl | none
    ],
];
