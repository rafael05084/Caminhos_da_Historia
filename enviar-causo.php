<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario'])) {
    header("Location:login.php");
    exit;
}

$mensagem = "";

// Carrega a lista de locais para o select
$sqlLocais = "SELECT id, nome FROM locais ORDER BY nome ASC";
$resLocais = mysqli_query($conexao, $sqlLocais);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $local_id = (int) $_POST['local_id'];
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    $usuario_id = $_SESSION['id'];

    $sql = "INSERT INTO causos (local_id, usuario_id, titulo, texto, status)
            VALUES ($local_id, $usuario_id, '$titulo', '$texto', 'pendente')";
    $resultado = mysqli_query($conexao, $sql);

    if ($resultado) {
        $mensagem = "Seu causo foi enviado! Ele ficará visível no mapa assim que um moderador aprovar.";
    } else {
        $mensagem = "Não foi possível enviar o causo: " . mysqli_error($conexao);
    }
}

// local pré-selecionado, se vier da URL (ex: enviar-causo.php?local_id=3)
$localPreSelecionado = isset($_GET['local_id']) ? (int) $_GET['local_id'] : null;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Enviar um causo - Caminhos da História</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=6">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="auth-page">

    <div class="auth-hero-text">
        <h2>Conte o seu causo</h2>
        <p class="subtitle">memórias da nossa gente</p>
        <p>Compartilhe uma história, lembrança ou memória ligada a um local histórico de Uruguaiana. Seu causo passa por aprovação de um moderador antes de aparecer no mapa.</p>

        <a href="inicial.php" class="auth-back"><button type="button">&larr; Voltar ao mapa</button></a>
    </div>

    <div class="auth-card">
        <form method="POST">
            <h1>Enviar causo</h1>

            <?php if ($mensagem): ?>
                <p class="admin-msg"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <select name="local_id" required>
                <option value="">Selecione o local...</option>
                <?php while ($local = mysqli_fetch_assoc($resLocais)): ?>
                    <option value="<?php echo $local['id']; ?>" <?php echo ($localPreSelecionado == $local['id']) ? 'selected' : ''; ?>>
                        <?php echo $local['nome']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="text" name="titulo" placeholder="Título do causo" required>
            <textarea name="texto" placeholder="Conte a sua história..." rows="6" required></textarea>

            <div class="auth-actions">
                <button type="submit">Enviar causo</button>
            </div>
        </form>
    </div>

</body>
</html>
