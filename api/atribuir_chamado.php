<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($data->id_chamado) || !isset($data->id_tecnico)) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

$id_chamado = (int)$data->id_chamado;
$id_tecnico = (int)$data->id_tecnico;
$prioridade = $conn->real_escape_string($data->prioridade);
$data_prevista = $conn->real_escape_string($data->data_prevista);

// Atualiza o chamado conforme HU06 e HU07
$sql = "UPDATE chamados SET 
        id_tecnico = ?, 
        prioridade = ?, 
        data_previsao_conclusao = ?, 
        status = 'agendado' 
        WHERE id_chamado = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $id_tecnico, $prioridade, $data_prevista, $id_chamado);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Chamado atribuído com sucesso!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar chamado."]);
}