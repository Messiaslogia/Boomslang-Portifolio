<?php

require __DIR__ . '/core/Router.php';
require __DIR__ . '/app/routes.php';

// base path
$basePath = '/boomslang';

$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedPath = substr($requestedPath, strlen($basePath));

if ($requestedPath === false || $requestedPath === '') {
    $requestedPath = '/';
}

// dispara o roteador
$router->dispatch($_SERVER['REQUEST_METHOD'], $requestedPath);
