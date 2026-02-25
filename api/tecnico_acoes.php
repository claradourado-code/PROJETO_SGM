<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

if ($acao === 'iniciar') {
    $data = json_decode(file_get_contents("php://input"));
    $id = (int)$data->id_chamado;
    $conn->query("UPDATE chamados SET status = 'em_execucao' WHERE id_chamado = $id");
    echo json_encode(["success" => true]);
} 
elseif ($acao === 'finalizar') {
    // Importante: FormData envia via $_POST
    $id_chamado = (int)$_POST['id_chamado'];
    $solucao = $conn->real_escape_string($_POST['solucao']);
    $tempo_dias = (int)($_POST['tempo_dias'] ?? 0);
    $minutos = $tempo_dias * 1440; // Converte dias para minutos conforme o banco

    $sql = "UPDATE chamados SET 
            status = 'concluido', 
            solucao_tecnica = '$solucao', 
            tempo_gasto_minutos = $minutos, 
            data_fechamento = NOW() 
            WHERE id_chamado = $id_chamado";
    
    if ($conn->query($sql)) {
        // Processamento de Fotos
        if (isset($_FILES['fotos']) || isset($_FILES['foto'])) {
            $diretorio = "../assets/uploads/";
            if (!is_dir($diretorio)) mkdir($diretorio, 0777, true);

            // Trata tanto 'foto' (única) quanto 'fotos[]' (múltiplas)
            $file_array = isset($_FILES['fotos']) ? $_FILES['fotos'] : $_FILES['foto'];
            
            // Se for arquivo único, normaliza para loop
            $names = (array)$file_array['name'];
            $tmp_names = (array)$file_array['tmp_name'];

            foreach ($tmp_names as $key => $tmp_name) {
                if (!empty($tmp_name)) {
                    $nome_arq = time() . "_" . $names[$key];
                    if (move_uploaded_file($tmp_name, $diretorio . $nome_arq)) {
                        $caminho_db = "assets/uploads/" . $nome_arq;
                        $conn->query("INSERT INTO chamados_anexos (id_chamado, caminho_arquivo, tipo_anexo) 
                                     VALUES ($id_chamado, '$caminho_db', 'conclusao')");
                    }
                }
            }
        }
        echo json_encode(["success" => true, "message" => "Serviço concluído!"]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}