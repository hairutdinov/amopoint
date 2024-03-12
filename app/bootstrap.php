<?php
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__."/entities"),
    isDevMode: true,
);

$connection = DriverManager::getConnection([
    'driver'   => 'pdo_mysql',
    'host'     => $_ENV['DB_HOST'] ?? '',
    'user'     => 'root',
    'password' => $_ENV['DB_ROOT_PASSWORD'] ?? '',
    'dbname'   => $_ENV['DB_NAME'] ?? '',
], $config);

$entityManager = new EntityManager($connection, $config);