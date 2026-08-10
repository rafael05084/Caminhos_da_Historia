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
    <title>Página Inicial</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .mapa-container {
            width: 100vw;
            height: 100vh;
            position: relative;
        }
        .mapa-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .topo {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            color: #fff;
            font-family: sans-serif;
        }
        .topo a {
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="mapa-container">
        <img src="mapauruguaiana.png" alt="Mapa interativo de Uruguaiana">

        
    </div>

</body>
</html>