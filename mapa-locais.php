<?php
require "conecta.php";

header("Content-Type: application/json; charset=utf-8");

$sql = "SELECT * FROM locais ORDER BY nome ASC";
$res = mysqli_query($conexao, $sql);

$locais = [];

while ($linha = mysqli_fetch_assoc($res)) {
    $linha['latitude'] = (float) $linha['latitude'];
    $linha['longitude'] = (float) $linha['longitude'];
    $locais[] = $linha;
}

echo json_encode($locais);
