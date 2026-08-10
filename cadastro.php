<?php
session_start();
require "conecta.php";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nome, usuario, senha) VALUES ('$nome', '$usuario', '$senha')";
    $resultado = mysqli_query($conexao,$sql);

    if($resultado && mysqli_affected_rows($conexao)>0){
        echo "Cadastro realizado com sucesso! <br> ";
        echo "<a href='loginatividade.php'> Faça seu login </a>"; 
    
    } else {
        echo "Falha ao se cadastrar: " . mysqli_error($conexao);
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=5">
</head>
<body class="auth-page">

    <a href="index.html" class="auth-back"><button type="button">&larr; Início</button></a>

    <div class="auth-hero-text">
        <h2>A memória de Uruguaiana</h2>
        <p class="subtitle">ao alcance de todos</p>
        <p>Explore monumentos, espaços históricos e locais que tiveram a sua história construída ao decorrer das gerações e ajude a reconstruir a história e memória local.</p>
    </div>

    <div class="auth-card">
        <form method="POST">
            <h1>Cadastre-se</h1>

            <input type="text" name="nome" placeholder="Nome completo" required>
            <br><br>
            <input type="email" name="usuario" placeholder="E-mail" required>
            <br><br>
            <input type="password" name="senha" placeholder="Senha" required>
            <br><br>
            <input type="password" name="confirmar_senha" placeholder="Confirmar senha" required>
            <br><br>

            <div class="auth-actions">
                <button type="submit">Cadastrar</button>
                <a href="login.php"><button type="button">Faça seu login</button></a>
            </div>
        </form>
    </div>

</body>
</html>