<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { exit; }

$id_chamado = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'];

if ($id_chamado > 0) {
    // Retorno para Detalhes (Objeto Único)
    $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome, u.nome as solicitante_nome 
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN blocos b ON a.id_bloco = b.id_bloco
            JOIN usuarios u ON c.id_solicitante = u.id_usuario
            WHERE c.id_chamado = $id_chamado";
    echo json_encode($conn->query($sql)->fetch_assoc());
} else {
    // Retorno para Listagem (Com contador de anexos)
    $where = ($perfil === 'solicitante') ? "WHERE c.id_solicitante = $user_id" : "";
    $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome,
            (SELECT COUNT(*) FROM chamados_anexos WHERE id_chamado = c.id_chamado) as total_anexos
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN blocos b ON a.id_bloco = b.id_bloco
            $where ORDER BY c.data_abertura DESC";
    echo json_encode($conn->query($sql)->fetch_all(MYSQLI_ASSOC));
}