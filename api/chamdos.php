<?php
include("config.php"); // Sua conexão com o banco
session_start();

$titulo    = $_POST['titulo'];
$descricao = $_POST['descricao'];
$id_bloco  = $_POST['id_bloco'];
$id_servico = $_POST['id_tipo_servico'];
$usuario_id = $_SESSION['usuario_id'];

// 1. Salva o Chamado
$sql = "INSERT INTO chamados (titulo, descricao, id_bloco, id_servico, id_usuario) 
        VALUES ('$titulo', '$descricao', '$id_bloco', '$id_servico', '$usuario_id')";

if (mysqli_query($conn, $sql)) {
    $id_chamado = mysqli_insert_id($conn);

    // 2. Lógica de Anexo
    if (!empty($_FILES['anexo']['name'])) {
        $pasta = "uploads/";
        $nome_arquivo = time() . "_" . $_FILES['anexo']['name'];
        if (move_uploaded_file($_FILES['anexo']['tmp_name'], $pasta . $nome_arquivo)) {
            mysqli_query($conn, "INSERT INTO chamados_anexos (id_chamado, arquivo_path) VALUES ('$id_chamado', '$nome_arquivo')");
        }
    }

    // 3. Redireciona para a página do chamado pronto
    header("Location: visualizar_chamado.php?id=" . $id_chamado);
} else {
    echo "Erro ao salvar: " . mysqli_error($conn);
}
?>