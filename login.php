<?php
session_start();
require "conecta.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // consulta o banco de dados com o usuário informado
    $sql = "SELECT * FROM usuarios WHERE usuario= '$usuario'";
    $res = mysqli_query($conexao, $sql);

    // se retornou algum resultado da busca...
    if(mysqli_num_rows($res)>0){ 
        $dados = mysqli_fetch_assoc($res);  // gera um vetor com os dados retornados    
    } else {
        header("Location:index.php?msg=Usuário ou senha incorretos.");
        exit;
    }

    // valida o usuário
    if (password_verify($senha, $dados['senha'])) {

        // cria as variáveis de sessão
        $_SESSION['id'] = $dados['id'];
        $_SESSION['usuario'] = $dados['usuario'];
        $_SESSION['nivel'] = $dados['nivel'];

        // verifica o nível antes de redirecionar
        switch ($dados['nivel']) {
            case 'administrador':
                header("Location:admin/admin.php");
                break;
            case 'moderador':
                header("Location:inicial.php");
                break;
            case 'comum':
                header("Location:inicial.php");
                break;
        }
        exit;

    } else {
        header("Location:index.php?msg=Usuário ou senha incorretos.");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=6">
</head>
<body class="auth-page">

    <div class="auth-hero-text">
        <h2>A memória de Uruguaiana</h2>
        <p class="subtitle">ao alcance de todos</p>
        <p>Explore monumentos, espaços históricos e locais que tiveram a sua história construída ao decorrer das gerações e ajude a reconstruir a história e memória local.</p>

        <a href="index.html" class="auth-back"><button type="button">&larr; Início</button></a>
    </div>

    <div class="auth-card">
        <form method="POST">
            <h1>Faça seu login</h1>

            <input type="email" name="usuario" placeholder="E-mail" required>
            <br><br>
            <input type="password" name="senha" placeholder="Senha" required>
            <br><br>

            <div class="auth-actions">
                <button type="submit">Entrar</button>
                <a href="cadastro.php"><button type="button">Criar conta</button></a>
            </div>
        </form>
    </div>

</body>
</html>