<?php
// api/tipos_servico.php
require_once '../config/database.php';
header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

if ($acao === 'listar') {
    $res = $conn->query("SELECT * FROM tipos_servico ORDER BY nome ASC");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}
elseif ($acao === 'salvar') {
    $data = json_decode(file_get_contents("php://input"));
    $nome = $conn->real_escape_string($data->nome);
    $desc = $conn->real_escape_string($data->descricao);
    $id   = (int)($data->id ?? 0);

    if ($id > 0) {
        $sql = "UPDATE tipos_servico SET nome = '$nome', descricao = '$desc' WHERE id_tipo = $id";
    } else {
        $sql = "INSERT INTO tipos_servico (nome, descricao) VALUES ('$nome', '$desc')";
    }
    echo json_encode(["success" => $conn->query($sql)]);
}
elseif ($acao === 'excluir') {
    $id = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM tipos_servico WHERE id_tipo = $id";
    echo json_encode(["success" => $conn->query($sql)]);
}
?>
