<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = (int)$_GET['id_chamado'];
    $sql = "SELECT c.texto, c.data_envio, u.nome FROM chamados_comentarios c 
            JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.id_chamado = $id ORDER BY c.data_envio ASC";
    echo json_encode($conn->query($sql)->fetch_all(MYSQLI_ASSOC));
} else {
    $data = json_decode(file_get_contents("php://input"));
    $texto = $conn->real_escape_string($data->comentario);
    $id_c = (int)$data->id_chamado;
    $id_u = $_SESSION['user_id'];
    $conn->query("INSERT INTO chamados_comentarios (id_chamado, id_usuario, texto) VALUES ($id_c, $id_u, '$texto')");
    echo json_encode(["success" => true]);
}