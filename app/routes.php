<?php

require __DIR__ . '../controllers/ControllerSkateSpot.php';
$ControllerSkateSpot = new ControllerSkateSpot();

$router = new Router();
// rotas
$router->get('/skateSpot', function () {
    require __DIR__ . '/../pages/skateSpot.php';
});

$router->get('/', function () {
    require __DIR__ . '/../pages/index.php';
});

$router->get('/spots', function () use ($ControllerSkateSpot) {

    $ControllerSkateSpot->getSpotNearby();
});

$router->post('/sendSpots', function () use ($ControllerSkateSpot) {
    // Pega o JSON do body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validação
    if (!$data || !isset($data['name']) || !isset($data['lat']) || !isset($data['lng'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Campos obrigatórios: name, lat, lng'
        ]);
        return;
    }

    // Adiciona no banco
    $result = $ControllerSkateSpot->addSpot($data);

    // Retorna JSON
    header('Content-Type: application/json');
    echo json_encode($result);
});

