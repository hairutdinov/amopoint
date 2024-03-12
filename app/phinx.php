<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return
[
    'paths' => [
        'migrations' => __DIR__ . '/migrations',
        'seeds' => __DIR__ . '/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '',
            'name' => $_ENV['DB_NAME'] ?? '',
            'user' => 'root',
            'pass' => $_ENV['DB_ROOT_PASSWORD'] ?? '',
            'port' => $_ENV['DB_EXPOSE_PORT'] ?? '',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
