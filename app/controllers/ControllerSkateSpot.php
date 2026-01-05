<?php
require_once __DIR__ . '/../config/db.php';
class ControllerSkateSpot
{

    private $pdo;

    public function __construct()
    {
        $db = new Db();
        $this->pdo = $db->pdo;
    }
    function getSpotNearby()
    {
        header('Content-Type: application/json');

        $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
        $lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
        $radiusKm = isset($_GET['radius_km']) ? floatval($_GET['radius_km']) : 10.0;
        $radiusKm = max(0.1, min($radiusKm, 20.0));

        if ($lat === null || $lng === null) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'error' => 'Parâmetros obrigatórios: lat e lng'
            ]);
            return;
        }

        try {
            $sql = "
                SELECT
                    s.id, s.name, s.description, s.category, s.lat, s.lng,
                    (6371 * ACOS(
                        COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(s.lng) - RADIANS(:lng))
                        + SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))
                    )) AS distance_km,
                    GROUP_CONCAT(si.url ORDER BY si.sort_order SEPARATOR '||') AS images_csv
                FROM spots s
                LEFT JOIN spot_images si ON si.spot_id = s.id
                GROUP BY s.id, s.name, s.description, s.category, s.lat, s.lng
                HAVING distance_km <= :radius
                ORDER BY distance_km ASC
                LIMIT 300
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':lat' => $lat,
                ':lng' => $lng,
                ':radius' => $radiusKm
            ]);

            $rows = $stmt->fetchAll();

            $spots = array_map(function ($r) {
                $r['images'] = empty($r['images_csv']) ? [] : explode('||', $r['images_csv']);
                unset($r['images_csv']);
                return $r;
            }, $rows);

            echo json_encode([
                'ok' => true,
                'count' => count($spots),
                'spots' => $spots
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'error' => 'Erro ao buscar spots'
            ]);
        }
    }

    function addSpot($data)
    {
        $name = isset($data['name']) ? $data['name'] : null;
        $description = isset($data['description']) ? $data['description'] : null;
        $lat = isset($data['lat']) ? $data['lat'] : null;
        $lng = isset($data['lng']) ? $data['lng'] : null;

        if ($name === null || $lat === null || $lng === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Os campos obrigatorios estão NUll'
            ]);
            exit;
        }

        try {
            $sql = "INSERT INTO spots (name, description, lat, lng) VALUES (:name, :description, :lat, :lng)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':lat' => $lat,
                ':lng' => $lng
            ]);



        } catch (\Throwable $th) {

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao adicinar Spot',
                'message' => $th
            ]);
        }
        return "Vou construir, só fui cortar o cabelo";
    }

}

?>