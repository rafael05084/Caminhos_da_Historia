<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] !== 'administrador') {
    header("Location:../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, usuario, senha, nivel) VALUES ('$nome', '$usuario', '$senha', 'moderador')";
    mysqli_query($conexao, $sql);

    if (mysqli_affected_rows($conexao) > 0) {
        $mensagem = "Moderador cadastrado com sucesso!";
    } else {
        $mensagem = "Falha ao cadastrar moderador.";
    }
}

if (isset($_GET['remover'])) {
    $id = $_GET['remover'];
    $sql = "DELETE FROM usuarios WHERE id = $id";
    mysqli_query($conexao, $sql);
    header("Location:admin.php");
    exit;
}

$sqlMod = "SELECT * FROM usuarios WHERE nivel = 'moderador'";
$resMod = mysqli_query($conexao, $sqlMod);
$sqlUsu = "SELECT * FROM usuarios WHERE nivel = 'comum'";
$resUsu = mysqli_query($conexao, $sqlUsu);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Administração</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/admin.css">

</head>
<body class="admin-page">

    <header class="admin-header">
        <div>
            <h1>Área do Administrador</h1>
            <span class="admin-sub">Caminhos da História — Uruguaiana</span>
        </div>
        <a href="../logout.php" class="admin-nav-sair">Sair</a>
    </header>

    <main class="admin-main">

        <section class="admin-card">
            <h2>Cadastrar novo moderador</h2>

            <?php if (!empty($mensagem)): ?>
                <p class="admin-msg"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="nome" placeholder="Nome do moderador" required>
                <input type="email" name="usuario" placeholder="E-mail do moderador" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Cadastrar moderador</button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Moderadores</h2>
            <table class="admin-table">
                <tr>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                </tr>
                <?php while ($mod = mysqli_fetch_assoc($resMod)): ?>
                <tr>
                    <td><?php echo $mod['nome']; ?></td>
                    <td><?php echo $mod['usuario']; ?></td>
                    <td><a href="admin.php?remover=<?php echo $mod['id']; ?>">Remover</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>

        <section class="admin-card">
            <h2>Usuários</h2>
            <table class="admin-table">
                <tr>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                </tr>
                <?php while ($usu = mysqli_fetch_assoc($resUsu)): ?>
                <tr>
                    <td><?php echo $usu['nome']; ?></td>
                    <td><?php echo $usu['usuario']; ?></td>
                    <td><a href="admin.php?remover=<?php echo $usu['id']; ?>">Remover</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>

    </main>

</body>
</html>