<?php
// 1. Configurações de Cabeçalho (Headers)
// Define que o retorno é JSON e aceita caracteres UTF-8
header('Content-Type: application/json; charset=utf-8');
// Permite acesso externo (CORS). Em produção, troque '*' pelo domínio do seu front-end se necessário.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// 2. Configurações do Banco de Dados
// ATENÇÃO: Preencha com os dados do seu servidor
$host = 'localhost';
$dbname = 'NOME_DO_SEU_BANCO';
$user = 'SEU_USUARIO';
$pass = 'SUA_SENHA';

// 3. Verifica se o parâmetro ID foi enviado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => true, 'mensagem' => 'O parâmetro ID é obrigatório.']);
    exit;
}

$id_chamado = intval($_GET['id']); // Sanitiza o ID para garantir que é um número

try {
    // 4. Conexão com o Banco de Dados (Usando PDO)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 5. Query SQL
    // IMPORTANTE: Substitua 'tabela_anexos' e 'id_chamado' pelos nomes reais da sua tabela
    $sql = "SELECT caminho_anexo, tipo_anexo 
            FROM tabela_anexos 
            WHERE id_chamado = :id_chamado";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_chamado', $id_chamado, PDO::PARAM_INT);
    $stmt->execute();

    // Busca todos os resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Retorno dos Dados
    if (count($resultados) > 0) {
        // Encontrou anexos
        echo json_encode([
            'sucesso' => true,
            'id_chamado' => $id_chamado,
            'quantidade' => count($resultados),
            'anexos' => $resultados
        ]);
    } else {
        // Não encontrou anexos para este ID
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Nenhum anexo encontrado para este chamado.',
            'anexos' => []
        ]);
    }

} catch (PDOException $e) {
    // Erro na conexão ou na query
    http_response_code(500); // Internal Server Error
    echo json_encode(['erro' => true, 'mensagem' => 'Erro interno: ' . $e->getMessage()]);
}
?>