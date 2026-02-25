<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Gestores
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$id_chamado = (int)($data->id_chamado ?? 0);
$acao = $data->acao ?? ''; 

if ($id_chamado > 0) {
    // Define o novo status baseado na ação enviada pelo botão
    $novo_status = ($acao === 'reabrir') ? 'aberto' : 'fechado';
    
    $sql = "UPDATE chamados SET status = ? WHERE id_chamado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $novo_status, $id_chamado);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status atualizado para $novo_status!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao atualizar status."]);
    }
}