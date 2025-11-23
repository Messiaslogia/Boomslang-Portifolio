<?php

require __DIR__ . '../controllers/ControllerSkateSpot.php';

$router = new Router();
// rotas
$router->get('/skateSpot', function () {
    require __DIR__ . '/../pages/skateSpot.php';
});

$router->get('/spots', function () {
    $controller = new ControllerSkateSpot();
    $controller->getSpotNearby();
});

// exemplo rota dinâmica
$router->get('/produto/{id}', function ($params) {
    require_once __DIR__ . '/../pages/produto.php';
});
