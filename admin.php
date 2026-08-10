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
        echo "Moderador cadastrado com sucesso! <br>";
    } else {
        echo "Falha ao cadastrar moderador. <br>";
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
</head>
<body>

    <h1>Área do Administrador</h1>

    <h2>Cadastrar novo moderador</h2>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome do moderador" required>
        <br><br>
        <input type="email" name="usuario" placeholder="E-mail do moderador" required>
        <br><br>
        <input type="password" name="senha" placeholder="Senha" required>
        <br><br>
        <button type="submit">Cadastrar moderador</button>
    </form>

    <h2>Moderadores</h2>

    <table border="1">
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

    <h2>Usuários</h2>

    <table border="1">
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

</body>
</html>