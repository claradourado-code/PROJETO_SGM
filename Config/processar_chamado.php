<?php
include("config.php"); // Sua conexão com o banco
session_start();

if ($_POST) {
    $titulo    = mysqli_real_escape_string($conn, $_POST['titulo']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
    $ambiente  = $_POST['id_ambiente'];
    $bloco     = $_POST['id_bloco'];
    $servico   = $_POST['id_servico'];
    $usuario   = $_SESSION['id_usuario']; // ID de quem está logado

    // Insere o chamado
    $sql = "INSERT INTO chamados (titulo, descricao, id_ambiente, id_bloco, id_servico, id_usuario) 
            VALUES ('$titulo', '$descricao', '$ambiente', '$bloco', '$servico', '$usuario')";

    if (mysqli_query($conn, $sql)) {
        $id_novo_chamado = mysqli_insert_id($conn);

        // TRATAMENTO DE ANEXO
        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
            $diretorio = "uploads/";
            $nome_arquivo = time() . "_" . $_FILES['arquivo']['name'];
            
            if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio . $nome_arquivo)) {
                mysqli_query($conn, "INSERT INTO chamados_anexos (id_chamado, arquivo_caminho) VALUES ('$id_novo_chamado', '$nome_arquivo')");
            }
        }

        // Redireciona para a página de sucesso ou para o próprio chamado
        header("Location: ver_chamado.php?id=" . $id_novo_chamado);
    } else {
        echo "Erro ao registrar: " . mysqli_error($conn);
    }
}
?>