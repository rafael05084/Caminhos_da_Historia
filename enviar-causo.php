<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario'])) {
    header("Location:login.php");
    exit;
}

$mensagem = "";
$enviado = isset($_GET['enviado']);

// Carrega a lista de locais para o select
$sqlLocais = "SELECT id, nome FROM locais ORDER BY nome ASC";
$resLocais = mysqli_query($conexao, $sqlLocais);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $local_id = (int) $_POST['local_id'];
    $titulo = mysqli_real_escape_string($conexao, $_POST['titulo']);
    $texto = mysqli_real_escape_string($conexao, $_POST['texto']);
    $usuario_id = (int) $_SESSION['id'];

    $sql = "INSERT INTO causos (local_id, usuario_id, titulo, texto, status)
            VALUES ($local_id, $usuario_id, '$titulo', '$texto', 'pendente')";
    $resultado = mysqli_query($conexao, $sql);

    if ($resultado) {
        // redireciona para não reenviar o formulário ao atualizar a página
        header("Location:enviar-causo.php?enviado=1");
        exit;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar um causo - Caminhos da História</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">

    <style>
        /* rótulo acima do campo de local */
        .campo-label {
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: -8px;
        }

        /* modal de causo enviado (centralizado na tela) */
        body.auth-page .modal-causo-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(0, 0, 0, 0.65);
        }

        .modal-causo-box {
            background-color: #132447;
            border-top: 5px solid #2e9e5b;
            border-radius: 14px;
            padding: 32px 36px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .modal-causo-icone {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background-color: #2e9e5b;
            color: #fff;
            font-size: 1.8rem;
            line-height: 56px;
        }

        .modal-causo-box h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: #6fcf97;
            margin-bottom: 10px;
        }

        .modal-causo-box .msg-sucesso {
            color: #6fcf97;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .modal-causo-box button {
            background-color: #2e9e5b;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 11px 40px;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .modal-causo-box button:hover {
            background-color: #278a4e;
        }
    </style>
</head>
<body class="auth-page" style="background-image: url('css/fundo.png');">

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

            <label for="local_id" class="campo-label">Nome do local do causo</label>
            <select name="local_id" id="local_id" required>
                <option value="">Selecione o local...</option>
                <?php while ($local = mysqli_fetch_assoc($resLocais)): ?>
                    <option value="<?php echo $local['id']; ?>" <?php echo ($localPreSelecionado == $local['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($local['nome']); ?>
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

    <?php if ($enviado): ?>
    <div class="modal-causo-overlay" id="modalCauso">
        <div class="modal-causo-box">
            <div class="modal-causo-icone">&#10003;</div>
            <h3>Causo enviado!</h3>
            <p class="msg-sucesso">Seu causo foi cadastrado com sucesso! Ele ficará visível no mapa assim que um moderador aprovar.</p>
            <button type="button" id="btnOkCauso">OK</button>
        </div>
    </div>

    <script>
        document.getElementById('btnOkCauso').addEventListener('click', function () {
            document.getElementById('modalCauso').style.display = 'none';
            history.replaceState(null, '', 'enviar-causo.php');
        });
    </script>
    <?php endif; ?>

</body>
</html>