<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

// 1. Identifica se veio um local_id pré-selecionado via GET
$localPreSelecionado = isset($_GET['local_id']) ? (int)$_GET['local_id'] : null;
$nomeLocalFixo = "";

if ($localPreSelecionado) {
    // Busca o nome do local fixo
    $stmtFixo = mysqli_prepare($conexao, "SELECT nome FROM locais WHERE id = ?");
    mysqli_stmt_bind_param($stmtFixo, "i", $localPreSelecionado);
    mysqli_stmt_execute($stmtFixo);
    $resFixo = mysqli_stmt_get_result($stmtFixo);

    if ($dadosFixo = mysqli_fetch_assoc($resFixo)) {
        $nomeLocalFixo = $dadosFixo['nome'];
    } else {
        // Se o id passado não existir no banco, cancela a pré-seleção
        $localPreSelecionado = null;
    }
}

// 2. Se não houver local pré-selecionado válido, busca a lista completa para o <select>
if (!$localPreSelecionado) {
    $resLocais = mysqli_query($conexao, "SELECT id, nome FROM locais ORDER BY nome ASC");
}

// 3. Processamento do formulário POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $local_id = (int) $_POST['local_id'];
    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    $usuario_id = (int) $_SESSION['id'];

    if (!empty($local_id) && !empty($titulo) && !empty($texto)) {
        // Uso de Prepared Statement para prevenir SQL Injection
        $stmt = mysqli_prepare($conexao, "INSERT INTO causos (local_id, usuario_id, titulo, texto, status) VALUES (?, ?, ?, ?, 'pendente')");
        mysqli_stmt_bind_param($stmt, "iiss", $local_id, $usuario_id, $titulo, $texto);

        if (mysqli_stmt_execute($stmt)) {
            $mensagem = "Seu causo foi enviado! Ele ficará visível no mapa assim que um moderador aprovar.";
        } else {
            $mensagem = "Não foi possível enviar o causo: " . mysqli_error($conexao);
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar um causo - Caminhos da História</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/style.css?v=6">

    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>">

    <style>
        <?php 
            if (file_exists("css/style.css")) { include "css/style.css"; }
            if (file_exists("css/admin.css")) { include "css/admin.css"; }
        ?>
    </style>
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
                <p class="admin-msg"><?php echo htmlspecialchars($mensagem); ?></p>
            <?php endif; ?>

            <?php if ($localPreSelecionado): ?>
                <input type="hidden" name="local_id" value="<?php echo $localPreSelecionado; ?>">
                <p style="color: #ffffff; font-weight: 600; margin-bottom: 15px;">
                    Local: <span style="color: #f2b6ab;"><?php echo htmlspecialchars($nomeLocalFixo); ?></span>
                </p>
            <?php else: ?>
                <select name="local_id" required>
                    <option value="">Selecione o local...</option>
                    <?php if (isset($resLocais)): ?>
                        <?php while ($local = mysqli_fetch_assoc($resLocais)): ?>
                            <option value="<?php echo $local['id']; ?>">
                                <?php echo htmlspecialchars($local['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            <?php endif; ?>

            <input type="text" name="titulo" placeholder="Título do causo" required>
            <textarea name="texto" placeholder="Conte a sua história..." rows="6" required></textarea>

            <div class="auth-actions">
                <button type="submit">Enviar causo</button>
            </div>
        </form>
    </div>

</body>
</html>