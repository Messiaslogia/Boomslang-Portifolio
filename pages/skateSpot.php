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

        /* Search Container - Centralizado no topo */
        .search-container {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .hamburger {
            width: 50px;
            height: 50px;
            background: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            /* Oculto por padrão no desktop */
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.4s ease;
            flex-shrink: 0;
        }

        .hamburger:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .hamburger span {
            width: 24px;
            height: 3px;
            background: #667eea;
            border-radius: 2px;
            transition: all 0.4s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(7px, 7px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
            transform: translateX(-10px);
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        .search-wrapper {
            width: 350px;
            /* Fixo no desktop */
            height: 50px;
            overflow: visible;
            /* Visível no desktop */
            transition: all 0.5s ease;
        }

        .search-wrapper.expanded {
            width: 350px;
        }

        .search-input {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
            border-radius: 25px;
            padding: 0 25px;
            font-size: 16px;
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transform: translateX(0);
            /* Visível no desktop */
            opacity: 1;
            /* Visível no desktop */
            transition: all 0.4s ease;
        }

        .search-wrapper.expanded .search-input {
            transform: translateX(0);
            opacity: 1;
            transition-delay: 0.1s;
        }

        .search-input::placeholder {
            color: #999;
        }

        .search-button {
            width: 50px;
            height: 50px;
            border: none;
            background: white;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transform: scale(1) rotate(0deg);
            /* Visível no desktop */
            opacity: 1;
            /* Visível no desktop */
            transition: all 0.5s ease;
            flex-shrink: 0;
        }

        .search-button.visible {
            transform: scale(1) rotate(0deg);
            opacity: 1;
            transition-delay: 0.2s;
        }

        .search-button:hover {
            transform: scale(1.05) rotate(0deg);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .search-button:active {
            transform: scale(0.95);
        }

        .search-icon {
            width: 24px;
            height: 24px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cpath d='m21 21-4.35-4.35'%3E%3C/path%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .search-container {
                top: 15px;
            }

            /* Ativa o hamburger no mobile */
            .hamburger {
                display: flex;
            }

            /* Esconde o input por padrão no mobile */
            .search-wrapper {
                width: 0;
                overflow: hidden;
            }

            .search-input {
                transform: translateX(-20px);
                opacity: 0;
            }

            /* Esconde o botão de busca por padrão no mobile */
            .search-button {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }

            /* Quando expandido no mobile */
            .search-wrapper.expanded {
                width: 280px;
            }

            .search-wrapper.expanded .search-input {
                transform: translateX(0);
                opacity: 1;
                transition-delay: 0.1s;
            }

            .hamburger,
            .search-button {
                width: 45px;
                height: 45px;
            }

            .hamburger span {
                width: 20px;
            }

            .controls {
                top: 80px;
                right: 10px;
                max-width: calc(100% - 20px);
            }
        }

        @media (max-width: 480px) {
            .search-container {
                top: 10px;
                gap: 10px;
            }

            .search-wrapper.expanded {
                width: 200px;
            }

            .hamburger,
            .search-button {
                width: 40px;
                height: 40px;
            }

            .search-input {
                font-size: 14px;
                padding: 0 20px;
            }

            .hamburger span {
                width: 18px;
                height: 2px;
            }

            .search-icon {
                width: 20px;
                height: 20px;
            }

            .controls {
                top: 70px;
                font-size: 13px;
            }

            .controls button {
                padding: 8px;
                font-size: 13px;
            }
        }

        @media (max-width: 360px) {
            .search-wrapper.expanded {
                width: 160px;
            }

            .search-container {
                gap: 8px;
            }
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

    <!-- Input -->
    <div class="search-container">
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="search-wrapper" id="searchWrapper">
            <input type="text" class="search-input" id="searchInput" placeholder="Digite o nome da rua...">
        </div>

        <button class="search-button" id="searchButton" onclick="buscar()">
            <div class="search-icon"></div>
        </button>
    </div>

    <!-- Mapa -->
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Seu JavaScript -->
    <script src="/boomslang/assets/js/app-skateSpot.js"></script>

    <script>
        async function buscar() {
            const endereco = document.getElementById('searchInput').value;

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

    <script>
        const hamburger = document.getElementById('hamburger');
        const searchWrapper = document.getElementById('searchWrapper');
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');

        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            searchWrapper.classList.toggle('expanded');
            searchButton.classList.toggle('visible');

            if (searchWrapper.classList.contains('expanded')) {
                setTimeout(() => {
                    searchInput.focus();
                }, 500);
            } else {
                searchInput.value = '';
            }
        });

        searchButton.addEventListener('click', function (e) {
            e.stopPropagation();
            const searchValue = searchInput.value.trim();

            if (searchValue) {
                console.log('Buscando por:', searchValue);
            }
        });

        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function (e) {
            if (!searchWrapper.contains(e.target) &&
                !hamburger.contains(e.target) &&
                !searchButton.contains(e.target)) {
                if (searchWrapper.classList.contains('expanded')) {
                    hamburger.classList.remove('active');
                    searchWrapper.classList.remove('expanded');
                    searchButton.classList.remove('visible');
                    searchInput.value = '';
                }
            }
        });
    </script>

</body>

</html>