<?php
return [
    'openapi' => '3.0.0',
    'info' => [
        'title' => 'Scoop Nation API',
        'description' => 'API for Scoop Nation E-commerce Platform',
        'version' => '1.0.0',
        'contact' => [
            'email' => 'wmkhan101@gmail.com'
        ],
    ],
    'servers' => [
        ['url' => 'http://localhost:8080', 'description' => 'Local development server'],
        ['url' => 'https://api.scoopnation.com', 'description' => 'Production server']
    ],
    'paths' => [],
    'components' => [
        'schemas' => []
    ]
];