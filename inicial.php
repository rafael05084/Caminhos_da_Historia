<?php
session_start();

if(isset($_SESSION['usuario'])){
    // continua a execução, a página é exibida abaixo
}else{
   header("Location: login.php");
        exit('ACESSO NEGADO');
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Histórico - Caminhos da História</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="mapa.css">
</head>
<body class="mapa-page">

    <header class="mapa-header">
        <div>
            <h1>Caminhos da História</h1>
            <span class="mapa-sub">Mapa Histórico — Uruguaiana</span>
        </div>
        <a href="index.html" class="mapa-voltar">&larr; Início</a>
    </header>

    <div class="mapa-toolbar">
        <input type="text" id="pesquisa-local" placeholder="🔎 Pesquisar local...">

        <div class="mapa-filtros">
            <button class="filtro-btn ativo" data-categoria="Todos">Todos</button>
            <button class="filtro-btn" data-categoria="Patrimônio Histórico">Patrimônio Histórico</button>
            <button class="filtro-btn" data-categoria="Patrimônio Religioso">Patrimônio Religioso</button>
            <button class="filtro-btn" data-categoria="Cultura">Cultura</button>
            <button class="filtro-btn" data-categoria="Tradição Gaúcha">Tradição Gaúcha</button>
            <button class="filtro-btn" data-categoria="Turismo">Turismo</button>
            <button class="filtro-btn" data-categoria="Integração Regional">Integração Regional</button>
        </div>
    </div>

    <div id="mapa"></div>

    <!-- Modal de detalhes do local -->
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal-box">
            <button class="modal-fechar" id="modal-fechar">&times;</button>
            <span class="modal-categoria" id="modal-categoria"></span>
            <h2 id="modal-nome"></h2>

            <h4>História</h4>
            <p id="modal-historia"></p>

            <h4>Importância para Uruguaiana</h4>
            <p id="modal-importancia"></p>

            <h4>Período histórico</h4>
            <p id="modal-periodo"></p>

            <h4>Curiosidades</h4>
            <p id="modal-curiosidades"></p>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="mapa.js"></script>

</body>
</html>