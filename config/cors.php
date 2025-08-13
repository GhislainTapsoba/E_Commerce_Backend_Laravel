<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], // ou ['*'] pour dev (moins sécurisé)
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];