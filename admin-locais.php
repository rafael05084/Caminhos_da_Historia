<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['nivel'], ['administrador', 'moderador'])) {
    header("Location:login.php");
    exit;
}

$pastaUploads = "uploads/locais/";
if (!is_dir($pastaUploads)) {
    mkdir($pastaUploads, 0755, true);
}

$extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

function salvarImagem($campo, $pasta, $extensoesPermitidas) {
    if (empty($_FILES[$campo]['name'])) return null;

    $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensoesPermitidas)) return null;

    $nomeArquivo = uniqid('local_') . '.' . $ext;
    $caminhoDestino = $pasta . $nomeArquivo;

    if (move_uploaded_file($_FILES[$campo]['tmp_name'], $caminhoDestino)) {
        return $caminhoDestino;
    }
    return null;
}

// ---------- Cadastrar ou atualizar local ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_local') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $historia = $_POST['historia'];
    $categoria = $_POST['categoria'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $periodo_historico = $_POST['periodo_historico'];
    $curiosidades = $_POST['curiosidades'];
    $importancia = $_POST['importancia'];

    $novaImagem = salvarImagem('imagem_upload', $pastaUploads, $extensoesPermitidas);

    if (!empty($_POST['id'])) {
        // edição
        $id = (int) $_POST['id'];

        $sqlImagem = $novaImagem ? ", imagem = '$novaImagem'" : "";

        $sql = "UPDATE locais SET
                    nome = '$nome',
                    descricao = '$descricao',
                    historia = '$historia',
                    categoria = '$categoria',
                    latitude = '$latitude',
                    longitude = '$longitude',
                    periodo_historico = '$periodo_historico',
                    curiosidades = '$curiosidades',
                    importancia = '$importancia'
                    $sqlImagem
                WHERE id = $id";
        mysqli_query($conexao, $sql);

        header("Location:admin-locais.php?editar=$id");
        exit;
    } else {
        // cadastro novo
        $imagemInsert = $novaImagem ?? '';
        $sql = "INSERT INTO locais
                    (nome, descricao, historia, categoria, latitude, longitude, periodo_historico, curiosidades, importancia, imagem)
                VALUES
                    ('$nome', '$descricao', '$historia', '$categoria', '$latitude', '$longitude', '$periodo_historico', '$curiosidades', '$importancia', '$imagemInsert')";
        mysqli_query($conexao, $sql);

        header("Location:admin-locais.php");
        exit;
    }
}

// ---------- Adicionar imagem da linha do tempo ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar_timeline') {
    $local_id = (int) $_POST['local_id'];
    $periodo = $_POST['periodo'];
    $legenda = $_POST['legenda'];

    $imagem = salvarImagem('imagem_timeline', $pastaUploads, $extensoesPermitidas);

    if ($imagem) {
        $sql = "INSERT INTO locais_imagens (local_id, periodo, imagem, legenda)
                VALUES ($local_id, '$periodo', '$imagem', '$legenda')";
        mysqli_query($conexao, $sql);
    }

    header("Location:admin-locais.php?editar=$local_id");
    exit;
}

// ---------- Excluir local ----------
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    mysqli_query($conexao, "DELETE FROM locais WHERE id = $id");
    mysqli_query($conexao, "DELETE FROM locais_imagens WHERE local_id = $id");
    header("Location:admin-locais.php");
    exit;
}

// ---------- Excluir imagem da linha do tempo ----------
if (isset($_GET['excluir_timeline'])) {
    $idImg = (int) $_GET['excluir_timeline'];
    $localId = (int) $_GET['local_id'];
    mysqli_query($conexao, "DELETE FROM locais_imagens WHERE id = $idImg");
    header("Location:admin-locais.php?editar=$localId");
    exit;
}

// ---------- Carregar local para edição ----------
$localEdicao = null;
$timelineLocal = [];
if (isset($_GET['editar'])) {
    $id = (int) $_GET['editar'];
    $res = mysqli_query($conexao, "SELECT * FROM locais WHERE id = $id");
    $localEdicao = mysqli_fetch_assoc($res);

    $resTimeline = mysqli_query($conexao, "SELECT * FROM locais_imagens WHERE local_id = $id ORDER BY ordem ASC, id ASC");
    if ($resTimeline) {
        while ($img = mysqli_fetch_assoc($resTimeline)) {
            $timelineLocal[] = $img;
        }
    }
}

$categorias = ['Patrimônio Histórico', 'Patrimônio Religioso', 'Patrimônios Imateriais', 'Cultura', 'Tradição Gaúcha', 'Turismo', 'Integração Regional'];

$sqlLocais = "SELECT * FROM locais ORDER BY nome ASC";
$resLocais = mysqli_query($conexao, $sqlLocais);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Locais - Caminhos da História</title>
    
    <!-- Importação de fontes externas do Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Chamada do CSS externo com parâmetro de quebra de cache -->
    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>">

    <!-- Injeção direta como plano de contingência para servidor local -->
    <style>
        <?php 
            if (file_exists("css/admin.css")) {
                include "css/admin.css"; 
            }
        ?>
    </style>
</head>
<body class="admin-page">

    <header class="admin-header">
        <div>
            <h1>Administrar Locais</h1>
            <span class="admin-sub">Caminhos da História — Uruguaiana</span>
        </div>
        <nav class="admin-nav">
            <a href="inicial.php">Ver mapa</a>
            <a href="admin-causos.php">Causos</a>
            <a href="admin.php">Usuários</a>
        </nav>
    </header>

    <main class="admin-main">

        <section class="admin-card">
            <h2><?php echo $localEdicao ? 'Editar local' : 'Cadastrar novo local'; ?></h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="salvar_local">
                <?php if ($localEdicao): ?>
                    <input type="hidden" name="id" value="<?php echo $localEdicao['id']; ?>">
                <?php endif; ?>

                <input type="text" name="nome" placeholder="Nome do local" value="<?php echo htmlspecialchars($localEdicao['nome'] ?? ''); ?>" required>

                <textarea name="descricao" placeholder="Descrição breve" rows="2" required><?php echo htmlspecialchars($localEdicao['descricao'] ?? ''); ?></textarea>

                <textarea name="historia" placeholder="História completa" rows="4"><?php echo htmlspecialchars($localEdicao['historia'] ?? ''); ?></textarea>

                <select name="categoria" required>
                    <?php foreach ($categorias as $cat):
                        $selecionado = (isset($localEdicao['categoria']) && $localEdicao['categoria'] === $cat) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cat; ?>" <?php echo $selecionado; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="latitude" placeholder="Latitude (ex: -29.7555)" value="<?php echo htmlspecialchars($localEdicao['latitude'] ?? ''); ?>" required>
                <input type="text" name="longitude" placeholder="Longitude (ex: -57.0878)" value="<?php echo htmlspecialchars($localEdicao['longitude'] ?? ''); ?>" required>

                <input type="text" name="periodo_historico" placeholder="Período histórico" value="<?php echo htmlspecialchars($localEdicao['periodo_historico'] ?? ''); ?>">

                <textarea name="curiosidades" placeholder="Curiosidades" rows="2"><?php echo htmlspecialchars($localEdicao['curiosidades'] ?? ''); ?></textarea>

                <textarea name="importancia" placeholder="Importância para Uruguaiana" rows="2"><?php echo htmlspecialchars($localEdicao['importancia'] ?? ''); ?></textarea>

                <label style="font-size:0.85rem; color:rgba(255,255,255,0.7);">Imagem principal do local</label>
                <input type="file" name="imagem_upload" accept=".jpg,.jpeg,.png,.webp">

                <?php if (!empty($localEdicao['imagem'])): ?>
                    <img src="<?php echo htmlspecialchars($localEdicao['imagem']); ?>" class="admin-img-preview" alt="Imagem atual">
                <?php endif; ?>

                <button type="submit"><?php echo $localEdicao ? 'Salvar alterações' : 'Cadastrar local'; ?></button>
            </form>
        </section>

        <?php if ($localEdicao): ?>
        <section class="admin-card">
            <h2>Linha do tempo de "<?php echo htmlspecialchars($localEdicao['nome']); ?>"</h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="adicionar_timeline">
                <input type="hidden" name="local_id" value="<?php echo $localEdicao['id']; ?>">

                <input type="text" name="periodo" placeholder="Período (ex: Década de 1940)" required>
                <input type="text" name="legenda" placeholder="Legenda da imagem">
                <input type="file" name="imagem_timeline" accept=".jpg,.jpeg,.png,.webp" required>

                <button type="submit">Adicionar imagem à linha do tempo</button>
            </form>

            <div style="margin-top: 20px;">
                <?php if (empty($timelineLocal)): ?>
                    <p class="admin-vazio">Nenhuma imagem cadastrada ainda para este local.</p>
                <?php endif; ?>

                <?php foreach ($timelineLocal as $img): ?>
                    <div class="timeline-admin-item">
                        <img src="<?php echo htmlspecialchars($img['imagem']); ?>" alt="<?php echo htmlspecialchars($img['periodo']); ?>">
                        <div class="timeline-info">
                            <div class="timeline-periodo"><?php echo htmlspecialchars($img['periodo']); ?></div>
                            <div class="timeline-legenda"><?php echo htmlspecialchars($img['legenda']); ?></div>
                        </div>
                        <a href="admin-locais.php?excluir_timeline=<?php echo $img['id']; ?>&local_id=<?php echo $localEdicao['id']; ?>">Excluir</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="admin-card">
            <h2>Locais cadastrados</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($local = mysqli_fetch_assoc($resLocais)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($local['nome']); ?></td>
                        <td><?php echo htmlspecialchars($local['categoria']); ?></td>
                        <td>
                            <a href="admin-locais.php?editar=<?php echo $local['id']; ?>">Editar</a> |
                            <a href="admin-locais.php?excluir=<?php echo $local['id']; ?>">Excluir</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

    </main>

</body>
</html>