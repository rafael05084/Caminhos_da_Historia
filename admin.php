<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] !== 'administrador') {
    header("Location:login.php");
    exit;
}

$mensagem = "";
$cadastrado = isset($_GET['cadastrado']);
$removido = (isset($_GET['removido']) && in_array($_GET['removido'], ['moderador', 'usuario'])) ? $_GET['removido'] : null;

// ---------- Cadastrar moderador ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $usuario = mysqli_real_escape_string($conexao, $_POST['usuario']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, usuario, senha, nivel) VALUES ('$nome', '$usuario', '$senha', 'moderador')";
    $resultado = mysqli_query($conexao, $sql);

    if ($resultado && mysqli_affected_rows($conexao) > 0) {
        // redireciona para não reenviar o formulário ao atualizar a página
        header("Location:admin.php?cadastrado=1");
        exit;
    } elseif (mysqli_errno($conexao) == 1062) {
        $mensagem = "Esse e-mail já está cadastrado.";
    } else {
        $mensagem = "Falha ao cadastrar moderador.";
    }
}

// ---------- Remover moderador ou usuário ----------
if (isset($_GET['remover'])) {
    $id = (int) $_GET['remover'];

    // só permite remover moderadores e usuários comuns (nunca o administrador)
    $resNivel = mysqli_query($conexao, "SELECT nivel FROM usuarios WHERE id = $id AND nivel IN ('moderador', 'comum')");
    $alvo = mysqli_fetch_assoc($resNivel);

    if ($alvo) {
        mysqli_query($conexao, "DELETE FROM usuarios WHERE id = $id");
        $tipo = ($alvo['nivel'] === 'moderador') ? 'moderador' : 'usuario';
        header("Location:admin.php?removido=$tipo");
    } else {
        header("Location:admin.php");
    }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>">

    <style>
        /* ---------- Modais (confirmação, cadastro e remoção) ---------- */
        .modal-adm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(0, 0, 0, 0.65);
        }

        .modal-adm-overlay.aberto {
            display: flex;
        }

        .modal-adm-box {
            background-color: #132447;
            border-top: 5px solid var(--cor);
            border-radius: 14px;
            padding: 32px 36px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            font-family: 'Inter', sans-serif;
        }

        .modal-adm-box.verde    { --cor: #2e9e5b; --txt: #6fcf97; }
        .modal-adm-box.vermelho { --cor: #C0392B; --txt: #ff8a7a; }
        .modal-adm-box.aviso    { --cor: #e0a83e; --txt: #f2c94c; }

        .modal-adm-icone {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background-color: var(--cor);
            color: #fff;
            font-size: 1.7rem;
            font-weight: 700;
            line-height: 56px;
        }

        .modal-adm-box h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--txt);
            margin: 0 0 10px 0;
        }

        .modal-adm-box p {
            color: var(--txt);
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0 0 22px 0;
        }

        .modal-adm-box.aviso p {
            color: rgba(255, 255, 255, 0.88);
        }

        .modal-adm-acoes {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .btn-modal {
            display: inline-block;
            border: none;
            border-radius: 999px;
            padding: 11px 32px;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            color: #fff;
            background-color: var(--cor);
        }

        .btn-modal:hover {
            filter: brightness(0.9);
        }

        .btn-modal.cancelar {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .btn-modal.perigo {
            background-color: #C0392B;
        }
    </style>
</head>
<body class="admin-page" style="background-image: linear-gradient(135deg, rgba(13,30,61,0.88) 0%, rgba(13,30,61,0.72) 50%, rgba(13,30,61,0.85) 100%), url('css/fundo.png'); background-size: cover; background-position: center; background-attachment: fixed;">

    <header class="admin-header">
        <div>
            <h1>Área do Administrador</h1>
            <span class="admin-sub">Caminhos da História — Uruguaiana</span>
        </div>
        <a href="logout.php" class="admin-nav-sair">Sair</a>
    </header>

    <main class="admin-main">

        <section class="admin-card">
            <h2>Cadastrar novo moderador</h2>

            <?php if (!empty($mensagem)): ?>
                <p class="admin-msg"><?php echo htmlspecialchars($mensagem); ?></p>
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
                    <td><?php echo htmlspecialchars($mod['nome']); ?></td>
                    <td><?php echo htmlspecialchars($mod['usuario']); ?></td>
                    <td><a href="admin.php?remover=<?php echo $mod['id']; ?>" class="link-remover" data-tipo="o moderador" data-nome="<?php echo htmlspecialchars($mod['nome']); ?>">Remover</a></td>
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
                    <td><?php echo htmlspecialchars($usu['nome']); ?></td>
                    <td><?php echo htmlspecialchars($usu['usuario']); ?></td>
                    <td><a href="admin.php?remover=<?php echo $usu['id']; ?>" class="link-remover" data-tipo="o usuário" data-nome="<?php echo htmlspecialchars($usu['nome']); ?>">Remover</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>

    </main>

    <!-- Aviso de segurança antes de remover -->
    <div class="modal-adm-overlay" id="modalConfirma">
        <div class="modal-adm-box aviso">
            <div class="modal-adm-icone">!</div>
            <h3>Tem certeza?</h3>
            <p id="textoConfirma"></p>
            <div class="modal-adm-acoes">
                <button type="button" class="btn-modal cancelar" id="btnCancelar">Cancelar</button>
                <a href="#" class="btn-modal perigo" id="btnConfirmar">Sim, remover</a>
            </div>
        </div>
    </div>

    <?php if ($cadastrado): ?>
    <!-- Moderador cadastrado (verde) -->
    <div class="modal-adm-overlay aberto" id="modalResultado">
        <div class="modal-adm-box verde">
            <div class="modal-adm-icone">&#10003;</div>
            <h3>Moderador cadastrado!</h3>
            <p>O moderador foi cadastrado com sucesso no sistema.</p>
            <div class="modal-adm-acoes">
                <button type="button" class="btn-modal" id="btnOkResultado">OK</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($removido): ?>
    <!-- Remoção concluída (vermelho) -->
    <div class="modal-adm-overlay aberto" id="modalResultado">
        <div class="modal-adm-box vermelho">
            <div class="modal-adm-icone">&#10005;</div>
            <h3><?php echo ($removido === 'moderador') ? 'Moderador removido!' : 'Usuário removido!'; ?></h3>
            <p><?php echo ($removido === 'moderador') ? 'O moderador foi removido do sistema.' : 'O usuário foi removido do sistema.'; ?></p>
            <div class="modal-adm-acoes">
                <button type="button" class="btn-modal" id="btnOkResultado">OK</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        var modalConfirma = document.getElementById('modalConfirma');
        var textoConfirma = document.getElementById('textoConfirma');
        var btnConfirmar = document.getElementById('btnConfirmar');

        // abre o aviso de segurança ao clicar em "Remover"
        document.querySelectorAll('.link-remover').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                textoConfirma.textContent = 'Deseja mesmo remover ' + link.dataset.tipo + ' "' + link.dataset.nome + '" do sistema? Essa ação não pode ser desfeita.';
                btnConfirmar.href = link.href;
                modalConfirma.classList.add('aberto');
            });
        });

        function fecharConfirma() {
            modalConfirma.classList.remove('aberto');
        }

        document.getElementById('btnCancelar').addEventListener('click', fecharConfirma);

        modalConfirma.addEventListener('click', function (e) {
            if (e.target === modalConfirma) fecharConfirma();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') fecharConfirma();
        });

        // fecha o modal de resultado (cadastro / remoção) e limpa a URL
        var btnOk = document.getElementById('btnOkResultado');
        if (btnOk) {
            btnOk.addEventListener('click', function () {
                document.getElementById('modalResultado').classList.remove('aberto');
                history.replaceState(null, '', 'admin.php');
            });
        }
    </script>

</body>
</html>