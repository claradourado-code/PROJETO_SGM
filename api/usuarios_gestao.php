<?php
// api/usuarios_gestao.php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$acao = $_GET['acao'] ?? '';

if ($acao === 'listar') {
    $res = $conn->query("SELECT id_usuario, nome, email, perfil, ativo FROM usuarios ORDER BY nome ASC");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}
elseif ($acao === 'salvar') {
    $data = json_decode(file_get_contents("php://input"));
    $nome = $conn->real_escape_string($data->nome);
    $email = $conn->real_escape_string($data->email);
    $perfil = $conn->real_escape_string($data->perfil);
    $id = (int)($data->id ?? 0);
    $senha = $data->senha ?? '';

    if ($id > 0) {
        $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', perfil = '$perfil' WHERE id_usuario = $id";
        $conn->query($sql);
        if(!empty($senha)) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $conn->query("UPDATE usuarios SET senha_hash = '$hash' WHERE id_usuario = $id");
        }
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, perfil, senha_hash) VALUES ('$nome', '$email', '$perfil', '$hash')";
        $conn->query($sql);
    }
    echo json_encode(["success" => true]);
}
elseif ($acao === 'excluir') {
    $id = (int)($_GET['id'] ?? 0);
    // Em vez de deletar, vamos apenas inativar
    $sql = "UPDATE usuarios SET ativo = 0 WHERE id_usuario = $id";
    echo json_encode(["success" => $conn->query($sql)]);
}
?>
