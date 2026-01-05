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
    if (!$data || !isset($data['name']) || !isset($data['lat']) || !isset($data['lng']) || !isset($data['description'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Campos obrigatórios: name, lat, lng, descripton'
        ]);
        return;
    }

    // Adiciona no banco
    $result = $ControllerSkateSpot->addSpot($data);

    // Retorna JSON
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->get('/searchLocalization', function () {
    header('Content-Type: application/json');

    $q = $_GET['q'] ?? null;
    $endereco = json_encode([$q]);

    $url = "https://nominatim.openstreetmap.org/search?format=json&q={$q}";
    $response = file_get_contents($url);
    print_r($response);
    exit;
    $data = json_decode($response, true);
    // echo $response;
    if (!empty($data)) {
        echo json_encode([
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon']
        ]);
    } else {
        echo json_encode(['erro' => 'Endereço não encontrado']);
    }

});

