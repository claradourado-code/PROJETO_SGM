<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Técnicos logados
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$id_tecnico = $_SESSION['user_id'];

// Seleciona chamados atribuídos ao técnico que não estão fechados ou cancelados
$sql = "SELECT c.id_chamado, c.descricao_problema, c.status, c.prioridade, 
               c.data_previsao_conclusao, a.nome as ambiente_nome, b.nome as bloco_nome
        FROM chamados c
        JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        JOIN blocos b ON a.id_bloco = b.id_bloco
        WHERE c.id_tecnico = $id_tecnico AND c.status NOT IN ('fechado', 'cancelado')
        ORDER BY FIELD(c.prioridade, 'urgente', 'alta', 'media', 'baixa'), c.data_previsao_conclusao ASC";

$result = $conn->query($sql);
$tarefas = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($tarefas);