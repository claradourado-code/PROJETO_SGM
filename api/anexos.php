<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$id_chamado = (int)$_GET['id_chamado'];

$sql = "SELECT caminho_arquivo, tipo_anexo FROM chamados_anexos WHERE id_chamado = $id_chamado";
$res = $conn->query($sql);

echo json_encode($res->fetch_all(MYSQLI_ASSOC));