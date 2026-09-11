<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['nivel'], ['administrador', 'moderador'])) {
    header("Location:login.php");
    exit;
}

// ---------- Cadastrar ou editar causo (feito pelo próprio moderador/admin) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_causo') {
    $local_id = (int) $_POST['local_id'];
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];

    if (!empty($_POST['id'])) {
        // edição de um causo já existente
        $id = (int) $_POST['id'];
        $sql = "UPDATE causos SET
                    local_id = $local_id,
                    titulo = '$titulo',
                    texto = '$texto'
                WHERE id = $id";
        mysqli_query($conexao, $sql);
    } else {
        // cadastro direto pelo moderador/administrador — já entra aprovado
        $usuario_id = $_SESSION['id'];
        $sql = "INSERT INTO causos (local_id, usuario_id, titulo, texto, status)
                VALUES ($local_id, $usuario_id, '$titulo', '$texto', 'aprovado')";
        mysqli_query($conexao, $sql);
    }

    header("Location:admin-causos.php");
    exit;
}

// ---------- Aprovar / Rejeitar ----------
if (isset($_GET['aprovar'])) {
    $id = (int) $_GET['aprovar'];
    mysqli_query($conexao, "UPDATE causos SET status = 'aprovado' WHERE id = $id");
    header("Location:admin-causos.php");
    exit;
}

if (isset($_GET['rejeitar'])) {
    $id = (int) $_GET['rejeitar'];
    mysqli_query($conexao, "UPDATE causos SET status = 'rejeitado' WHERE id = $id");
    header("Location:admin-causos.php");
    exit;
}

// ---------- Excluir ----------
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    mysqli_query($conexao, "DELETE FROM causos WHERE id = $id");
    header("Location:admin-causos.php");
    exit;
}

// ---------- Carregar causo para edição ----------
$causoEdicao = null;
if (isset($_GET['editar'])) {
    $id = (int) $_GET['editar'];
    $res = mysqli_query($conexao, "SELECT * FROM causos WHERE id = $id");
    $causoEdicao = mysqli_fetch_assoc($res);
}

// ---------- Locais para o select ----------
$sqlLocais = "SELECT id, nome FROM locais ORDER BY nome ASC";
$resLocais = mysqli_query($conexao, $sqlLocais);

// ---------- Listas ----------
$sqlPendentes = "SELECT c.*, l.nome AS local_nome, u.nome AS autor
                  FROM causos c
                  JOIN locais l ON l.id = c.local_id
                  JOIN usuarios u ON u.id = c.usuario_id
                  WHERE c.status = 'pendente'
                  ORDER BY c.data_cadastro ASC";
$resPendentes = mysqli_query($conexao, $sqlPendentes);

$sqlTodos = "SELECT c.*, l.nome AS local_nome, u.nome AS autor
                  FROM causos c
                  JOIN locais l ON l.id = c.local_id
                  JOIN usuarios u ON u.id = c.usuario_id
                  WHERE c.status != 'pendente'
                  ORDER BY c.data_cadastro DESC";
$resTodos = mysqli_query($conexao, $sqlTodos);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Moderador - Caminhos da História</title>
    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>">
</head>
<body class="admin-page">

    <header class="admin-header">
        <div>
            <h1>Painel do Moderador</h1>
            <span class="admin-sub">Caminhos da História — Uruguaiana</span>
        </div>
        <nav class="admin-nav">
            <a href="inicial.php">Ver mapa</a>
            <a href="admin-locais.php">Locais</a>
            <a href="logout.php" class="admin-nav-sair">Sair</a>
        </nav>
    </header>

    <main class="admin-main">

        <section class="admin-card">
            <h2><?php echo $causoEdicao ? 'Editar causo' : 'Cadastrar novo causo'; ?></h2>

            <form method="POST">
                <input type="hidden" name="acao" value="salvar_causo">
                <?php if ($causoEdicao): ?>
                    <input type="hidden" name="id" value="<?php echo $causoEdicao['id']; ?>">
                <?php endif; ?>

                <select name="local_id" required>
                    <option value="">Selecione o local...</option>
                    <?php while ($local = mysqli_fetch_assoc($resLocais)): ?>
                        <option value="<?php echo $local['id']; ?>"
                            <?php echo (isset($causoEdicao['local_id']) && $causoEdicao['local_id'] == $local['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($local['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="text" name="titulo" placeholder="Título do causo" value="<?php echo $causoEdicao['titulo'] ?? ''; ?>" required>

                <textarea name="texto" placeholder="Conte a história..." rows="5" required><?php echo $causoEdicao['texto'] ?? ''; ?></textarea>

                <button type="submit"><?php echo $causoEdicao ? 'Salvar alterações' : 'Cadastrar causo'; ?></button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Pendentes de aprovação</h2>

            <?php if (mysqli_num_rows($resPendentes) === 0): ?>
                <p class="admin-vazio">Nenhum causo pendente no momento.</p>
            <?php endif; ?>

            <?php while ($causo = mysqli_fetch_assoc($resPendentes)): ?>
                <div class="causo-item">
                    <div class="causo-item-topo">
                        <strong><?php echo $causo['titulo']; ?></strong>
                        <span class="causo-local"><?php echo $causo['local_nome']; ?></span>
                    </div>
                    <p><?php echo nl2br($causo['texto']); ?></p>
                    <div class="causo-meta">
                        Enviado por <?php echo $causo['autor']; ?> em <?php echo date('d/m/Y', strtotime($causo['data_cadastro'])); ?>
                    </div>
                    <div class="causo-acoes">
                        <a href="admin-causos.php?aprovar=<?php echo $causo['id']; ?>" class="btn-aprovar">Aprovar</a>
                        <a href="admin-causos.php?rejeitar=<?php echo $causo['id']; ?>" class="btn-rejeitar">Rejeitar</a>
                        <a href="admin-causos.php?editar=<?php echo $causo['id']; ?>" class="btn-rejeitar">Editar</a>
                        <a href="admin-causos.php?excluir=<?php echo $causo['id']; ?>" class="btn-rejeitar">Excluir</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>

        <section class="admin-card">
            <h2>Todos os causos (aprovados / rejeitados)</h2>
            <table class="admin-table">
                <tr>
                    <th>Título</th>
                    <th>Local</th>
                    <th>Autor</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
                <?php while ($causo = mysqli_fetch_assoc($resTodos)): ?>
                <tr>
                    <td><?php echo $causo['titulo']; ?></td>
                    <td><?php echo $causo['local_nome']; ?></td>
                    <td><?php echo $causo['autor']; ?></td>
                    <td><span class="status-<?php echo $causo['status']; ?>"><?php echo ucfirst($causo['status']); ?></span></td>
                    <td>
                        <a href="admin-causos.php?editar=<?php echo $causo['id']; ?>">Editar</a> |
                        <a href="admin-causos.php?excluir=<?php echo $causo['id']; ?>">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>

    </main>

</body>
</html>