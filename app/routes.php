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
    header('Content-Type: application/json; charset=utf-8');

    $q = $_GET['q'] ?? '';

    if (!$q) {
        http_response_code(400);
        echo json_encode(['erro' => 'Endereço não informado']);
        return;
    }

    $qClean = preg_replace('/[^0-9]/', '', $q);

    if (strlen($qClean) === 8) {
        $cepFormatado = substr($qClean, 0, 5) . '-' . substr($qClean, 5, 3);

        $viaCepUrl = "https://viacep.com.br/ws/{$qClean}/json/";

        $ch = curl_init($viaCepUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $viaCepResponse = curl_exec($ch);
        curl_close($ch);

        $viaCepData = json_decode($viaCepResponse, true);


        // Verifica se o CEP é válido
        if (isset($viaCepData['erro'])) {
            http_response_code(404);
            echo json_encode(['erro' => 'CEP não encontrado']);
            return;
        }

        // Monta endereço completo para buscar coordenadas
        $endereco = trim($viaCepData['logradouro']);
        $bairro = trim($viaCepData['bairro']);
        $cidade = trim($viaCepData['localidade']);
        $estado = trim($viaCepData['uf']);

        // Monta query com endereço completo
        $query = "{$endereco}, {$bairro}, {$cidade}, {$estado}, Brazil";
    } else {
        $query = $q . ', Brazil';
    }

    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($query) . '&countrycodes=br&addressdetails=1';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'BoomslangApp/1.0');

    $response = curl_exec($ch);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao consultar API']);
        curl_close($ch);
        return;
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data)) {
        echo json_encode([
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon'],
            'raw' => $data[0]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Endereço não encontrado']);
    }
});


