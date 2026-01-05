<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skate Spots</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #map {
            flex: 1;
            width: 100%;
        }

        .controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .controls button {
            display: block;
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            font-weight: bold;
        }

        .controls button:hover {
            background: #0056b3;
        }

        .controls input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .controls label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .popup-gallery {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
            overflow-x: auto;
        }

        .popup-gallery img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <!-- Controles -->
    <div class="controls">
        <button id="useLocationBtn">📍 Usar Minha Localização</button>

        <label for="radiusKm">Raio de busca (km):</label>
        <input type="number" id="radiusKm" value="3" min="0.5" max="20" step="0.5">

        <button id="reloadBtn">🔄 Recarregar Spots</button>

        <input id="endereco" placeholder="Digite o endereço">
        <button onclick="buscar()">Buscar</button>
    </div>

    <!-- Mapa -->
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Seu JavaScript -->
    <script src="/boomslang/assets/js/app-skateSpot.js"></script>

    <script>
        async function buscar() {
            const endereco = document.getElementById('endereco').value;

            try {
                const res = await fetch(
                    `/boomslang/searchLocalization?q=${encodeURIComponent(endereco)}`
                );

                const data = await res.json();

                if (data.erro) {
                    console.error('Erro:', data.erro);
                    return;
                }

                console.log('Latitude:', data.lat);
                console.log('Longitude:', data.lng);
                console.log('Resposta completa:', data.raw);

            } catch (err) {
                console.error('Erro no fetch:', err);
            }
        }
    </script>

</body>

</html>