<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['nivel'], ['administrador', 'moderador'])) {
    header("Location:login.php");
    exit;
}

// ---------- Cadastrar ou atualizar ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $historia = $_POST['historia'];
    $categoria = $_POST['categoria'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $periodo_historico = $_POST['periodo_historico'];
    $curiosidades = $_POST['curiosidades'];
    $importancia = $_POST['importancia'];

    if (!empty($_POST['id'])) {
        // edição
        $id = $_POST['id'];
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
                WHERE id = $id";
        mysqli_query($conexao, $sql);
    } else {
        // cadastro novo
        $sql = "INSERT INTO locais
                    (nome, descricao, historia, categoria, latitude, longitude, periodo_historico, curiosidades, importancia)
                VALUES
                    ('$nome', '$descricao', '$historia', '$categoria', '$latitude', '$longitude', '$periodo_historico', '$curiosidades', '$importancia')";
        mysqli_query($conexao, $sql);
    }

    header("Location:admin-locais.php");
    exit;
}

// ---------- Excluir ----------
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM locais WHERE id = $id";
    mysqli_query($conexao, $sql);
    header("Location:admin-locais.php");
    exit;
}

// ---------- Carregar local para edição ----------
$localEdicao = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $sql = "SELECT * FROM locais WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $localEdicao = mysqli_fetch_assoc($res);
}

$sqlLocais = "SELECT * FROM locais ORDER BY nome ASC";
$resLocais = mysqli_query($conexao, $sqlLocais);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Administrar Locais - Caminhos da História</title>
    <style>
        body { font-family: sans-serif; background:#0d1e3d; color:#fff; padding: 24px; }
        h1, h2 { font-family: Georgia, serif; }
        form { background:#132447; padding:20px; border-radius:10px; max-width:600px; margin-bottom:32px; }
        form input, form textarea, form select { width:100%; padding:8px; margin-bottom:12px; border-radius:6px; border:none; }
        form button { background:#C0392B; color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; }
        table { border-collapse: collapse; width:100%; background:#132447; }
        table, th, td { border:1px solid rgba(255,255,255,0.15); }
        th, td { padding:8px; text-align:left; }
        a { color:#f2b6ab; }
    </style>
</head>
<body>

    <h1>Administrar Locais do Mapa</h1>
    <p><a href="inicial.php">&larr; Ver mapa</a> | <a href="admin.php">Área do administrador</a></p>

    <h2><?php echo $localEdicao ? 'Editar local' : 'Cadastrar novo local'; ?></h2>

    <form method="POST">
        <?php if ($localEdicao): ?>
            <input type="hidden" name="id" value="<?php echo $localEdicao['id']; ?>">
        <?php endif; ?>

        <input type="text" name="nome" placeholder="Nome do local" value="<?php echo $localEdicao['nome'] ?? ''; ?>" required>

        <textarea name="descricao" placeholder="Descrição breve" required><?php echo $localEdicao['descricao'] ?? ''; ?></textarea>

        <textarea name="historia" placeholder="História completa"><?php echo $localEdicao['historia'] ?? ''; ?></textarea>

        <select name="categoria" required>
            <?php
            $categorias = ['Patrimônio Histórico', 'Patrimônio Religioso', 'Cultura', 'Tradição Gaúcha', 'Turismo', 'Integração Regional'];
            foreach ($categorias as $cat):
                $selecionado = (isset($localEdicao['categoria']) && $localEdicao['categoria'] === $cat) ? 'selected' : '';
            ?>
                <option value="<?php echo $cat; ?>" <?php echo $selecionado; ?>><?php echo $cat; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="latitude" placeholder="Latitude (ex: -29.7555)" value="<?php echo $localEdicao['latitude'] ?? ''; ?>" required>
        <input type="text" name="longitude" placeholder="Longitude (ex: -57.0878)" value="<?php echo $localEdicao['longitude'] ?? ''; ?>" required>

        <input type="text" name="periodo_historico" placeholder="Período histórico" value="<?php echo $localEdicao['periodo_historico'] ?? ''; ?>">

        <textarea name="curiosidades" placeholder="Curiosidades"><?php echo $localEdicao['curiosidades'] ?? ''; ?></textarea>

        <textarea name="importancia" placeholder="Importância para Uruguaiana"><?php echo $localEdicao['importancia'] ?? ''; ?></textarea>

        <button type="submit"><?php echo $localEdicao ? 'Salvar alterações' : 'Cadastrar local'; ?></button>
    </form>

    <h2>Locais cadastrados</h2>

    <table>
        <tr>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Ação</th>
        </tr>
        <?php while ($local = mysqli_fetch_assoc($resLocais)): ?>
        <tr>
            <td><?php echo $local['nome']; ?></td>
            <td><?php echo $local['categoria']; ?></td>
            <td>
                <a href="admin-locais.php?editar=<?php echo $local['id']; ?>">Editar</a> |
                <a href="admin-locais.php?excluir=<?php echo $local['id']; ?>">Excluir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
