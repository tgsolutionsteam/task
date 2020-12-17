<?php

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/db/migrations'
        ],
        'seeds' => [
            __DIR__ . '/db/seeds'
        ]
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'main',
        'main' => [
            'adapter' => 'mysql',
            'host' => array_key_exists('MYSQL_HOST', $_SERVER) ? $_SERVER['MYSQL_HOST'] : 'mysql',
            'user' => array_key_exists('MYSQL_USER', $_SERVER) ? $_SERVER['MYSQL_USER'] : 'task',
            'pass' => array_key_exists('MYSQL_PASSWORD', $_SERVER) ?
                $_SERVER['MYSQL_PASSWORD'] :
                'KwQgcTvYBO4yuBB43VCHS2g5',
            'name' => array_key_exists('MYSQL_DATABASE', $_SERVER) ? $_SERVER['MYSQL_DATABASE'] : 'task',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ]
    ]
];
