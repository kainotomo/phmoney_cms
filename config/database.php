<?php

use Illuminate\Support\Str;

return [

    'phmoney_portfolio' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET_ACS', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => 'phmprt_',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ],

    'phmoney_sqlite' => [
        'driver' => 'sqlite',
        'url' => env('DATABASE_URL_PORTFOLIO'),
        'database' => env('DB_DATABASE_PORTFOLIO', storage_path('app/import/sqlite/')),
        'prefix' => '',
        'foreign_key_constraints' => env('DB_FOREIGN_KEYS_PORTFOLIO', true),
    ],

];
