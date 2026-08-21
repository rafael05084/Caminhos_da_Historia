<?php
require "conecta.php";

header("Content-Type: application/json; charset=utf-8");

$sql = "SELECT * FROM locais ORDER BY nome ASC";
$res = mysqli_query($conexao, $sql);

$locais = [];

while ($linha = mysqli_fetch_assoc($res)) {
    $linha['latitude'] = (float) $linha['latitude'];
    $linha['longitude'] = (float) $linha['longitude'];

    // ---------- Causos aprovados deste local ----------
    $sqlCausos = "SELECT c.id, c.titulo, c.texto, c.data_cadastro, u.nome AS autor
                  FROM causos c
                  JOIN usuarios u ON u.id = c.usuario_id
                  WHERE c.local_id = " . (int) $linha['id'] . "
                    AND c.status = 'aprovado'
                  ORDER BY c.data_cadastro DESC";
    $resCausos = mysqli_query($conexao, $sqlCausos);
    $causos = [];
    if ($resCausos) {
        while ($causo = mysqli_fetch_assoc($resCausos)) {
            $causos[] = $causo;
        }
    }
    $linha['causos'] = $causos;

    // ---------- Linha do tempo (imagens por período) ----------
    $sqlImagens = "SELECT id, periodo, imagem, legenda
                   FROM locais_imagens
                   WHERE local_id = " . (int) $linha['id'] . "
                   ORDER BY ordem ASC, id ASC";
    $resImagens = mysqli_query($conexao, $sqlImagens);
    $imagens = [];
    if ($resImagens) {
        while ($img = mysqli_fetch_assoc($resImagens)) {
            $imagens[] = $img;
        }
    }
    $linha['linha_do_tempo'] = $imagens;

    $locais[] = $linha;
}

echo json_encode($locais);