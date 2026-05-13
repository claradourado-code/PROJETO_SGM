<?php
// api/localizacoes.php
require_once '../config/database.php';
header('Content-Type: application/json');
$acao = $_GET['acao'] ?? '';
if ($acao === 'listar_blocos') {
    $res = $conn->query("SELECT id_bloco, nome FROM blocos");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
} 
elseif ($acao === 'listar_ambientes') {
    $id_bloco = (int)($_GET['id_bloco'] ?? 0);
    $stmt = $conn->prepare("SELECT id_ambiente, nome FROM ambientes 
    WHERE id_bloco = ?");
    $stmt->bind_param("i", $id_bloco);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
}
elseif ($acao === 'listar_tipos') {
    $res = $conn->query("SELECT id_tipo, nome FROM tipos_servico");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}
elseif ($acao === 'salvar_bloco') {
    $data = json_decode(file_get_contents("php://input"));
    $nome = $conn->real_escape_string($data->nome);
    $sql = "INSERT INTO blocos (nome) VALUES ('$nome')";
    echo json_encode(["success" => $conn->query($sql)]);
}
elseif ($acao === 'salvar_ambiente') {
    $data = json_decode(file_get_contents("php://input"));
    $nome = $conn->real_escape_string($data->nome);
    $id_bloco = (int)$data->id_bloco;
    $sql = "INSERT INTO ambientes (nome, id_bloco) VALUES ('$nome', $id_bloco)";
    echo json_encode(["success" => $conn->query($sql)]);
}
elseif ($acao === 'excluir_bloco') {
    $id = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM blocos WHERE id_bloco = $id";
    echo json_encode(["success" => $conn->query($sql)]);
}
elseif ($acao === 'excluir_ambiente') {
    $id = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM ambientes WHERE id_ambiente = $id";
    echo json_encode(["success" => $conn->query($sql)]);
}
?>