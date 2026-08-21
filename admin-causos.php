<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['nivel'], ['administrador', 'moderador'])) {
    header("Location:login.php");
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

$sqlPendentes = "SELECT c.*, l.nome AS local_nome, u.nome AS autor
                  FROM causos c
                  JOIN locais l ON l.id = c.local_id
                  JOIN usuarios u ON u.id = c.usuario_id
                  WHERE c.status = 'pendente'
                  ORDER BY c.data_cadastro ASC";
$resPendentes = mysqli_query($conexao, $sqlPendentes);

$sqlHistorico = "SELECT c.*, l.nome AS local_nome, u.nome AS autor
                  FROM causos c
                  JOIN locais l ON l.id = c.local_id
                  JOIN usuarios u ON u.id = c.usuario_id
                  WHERE c.status != 'pendente'
                  ORDER BY c.data_cadastro DESC
                  LIMIT 30";
$resHistorico = mysqli_query($conexao, $sqlHistorico);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovar Causos - Caminhos da História</title>
    
    <!-- Importação de fontes externas do Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Merriweather:wght@700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    
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
            <h1>Aprovar Causos</h1>
            <span class="admin-sub">Caminhos da História — Uruguaiana</span>
        </div>
        <nav class="admin-nav">
            <a href="inicial.php">Ver mapa</a>
            <a href="admin-locais.php">Locais</a>
            <a href="admin.php">Usuários</a>
        </nav>
    </header>

    <main class="admin-main">

        <section class="admin-card">
            <h2>Pendentes de aprovação</h2>

            <?php if (mysqli_num_rows($resPendentes) === 0): ?>
                <p class="admin-vazio">Nenhum causo pendente no momento.</p>
            <?php endif; ?>

            <?php while ($causo = mysqli_fetch_assoc($resPendentes)): ?>
                <div class="causo-item">
                    <div class="causo-item-topo">
                        <strong><?php echo htmlspecialchars($causo['titulo']); ?></strong>
                        <span class="causo-local"><?php echo htmlspecialchars($causo['local_nome']); ?></span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($causo['texto'])); ?></p>
                    <div class="causo-meta">
                        Enviado por <?php echo htmlspecialchars($causo['autor']); ?> em <?php echo date('d/m/Y', strtotime($causo['data_cadastro'])); ?>
                    </div>
                    <div class="causo-acoes">
                        <a href="admin-causos.php?aprovar=<?php echo $causo['id']; ?>" class="btn-aprovar">Aprovar</a>
                        <a href="admin-causos.php?rejeitar=<?php echo $causo['id']; ?>" class="btn-rejeitar">Rejeitar</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>

        <section class="admin-card">
            <h2>Histórico recente</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Local</th>
                        <th>Autor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($causo = mysqli_fetch_assoc($resHistorico)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($causo['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($causo['local_nome']); ?></td>
                        <td><?php echo htmlspecialchars($causo['autor']); ?></td>
                        <td><span class="status-<?php echo $causo['status']; ?>"><?php echo ucfirst($causo['status']); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

    </main>

</body>
</html>