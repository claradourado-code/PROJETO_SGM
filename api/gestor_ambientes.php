<?php
session_start();

// Cria a lista de ambientes se ela ainda não existir
if (!isset($_SESSION['ambientes'])) {
    $_SESSION['ambientes'] = [];
}

$mensagem = "";

// 1. CADASTRAR AMBIENTE (via formulário POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_ambiente = htmlspecialchars(trim($_POST['nome_ambiente']));
    $capacidade = htmlspecialchars(trim($_POST['capacidade']));

    if (!empty($nome_ambiente) && !empty($capacidade)) {
        $_SESSION['ambientes'][] = [
            'nome' => $nome_ambiente,
            'capacidade' => $capacidade,
            'status' => 'Livre' // Todo ambiente começa livre
        ];
        $mensagem = "<p class='sucesso'>Ambiente '$nome_ambiente' cadastrado com sucesso!</p>";
    } else {
        $mensagem = "<p class='erro'>Por favor, preencha todos os campos.</p>";
    }
}

// 2. GERENCIAR AÇÕES (via links GET)
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Verifica se o ambiente realmente existe na nossa lista
    if (isset($_SESSION['ambientes'][$id])) {
        
        // Ação de Excluir o ambiente específico
        if ($_GET['acao'] === 'excluir') {
            unset($_SESSION['ambientes'][$id]);
            $mensagem = "<p class='sucesso'>Ambiente removido com sucesso!</p>";
        }
        
        // Ação de Alterar o Status
        if ($_GET['acao'] === 'status') {
            if ($_SESSION['ambientes'][$id]['status'] === 'Livre') {
                $_SESSION['ambientes'][$id]['status'] = 'Ocupado';
            } else {
                $_SESSION['ambientes'][$id]['status'] = 'Livre';
            }
            // Redireciona para limpar a URL
            header("Location: gestor_ambiente.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Ambientes</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        input { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; }
        .sucesso { color: green; font-weight: bold; }
        .erro { color: red; font-weight: bold; }
        .btn-acao { padding: 6px 10px; text-decoration: none; color: white; border-radius: 4px; font-size: 14px; }
        .btn-status { background-color: #007bff; }
        .btn-status:hover { background-color: #0056b3; }
        .btn-excluir { background-color: #dc3545; }
        .btn-excluir:hover { background-color: #c82333; }
        .status-livre { color: green; font-weight: bold; }
        .status-ocupado { color: orange; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestor de Ambientes</h1>
    
    <?= $mensagem ?>

    <form action="gestor_ambiente.php" method="POST">
        <label for="nome_ambiente">Nome do Ambiente/Sala:</label>
        <input type="text" id="nome_ambiente" name="nome_ambiente" placeholder="Ex: Laboratório de Informática" required>
        
        <label for="capacidade">Capacidade (Pessoas):</label>
        <input type="number" id="capacidade" name="capacidade" placeholder="Ex: 30" required>
        
        <button type="submit">Cadastrar Ambiente</button>
    </form>

    <hr>

    <h2>Gerenciamento</h2>
    
    <?php if (count($_SESSION['ambientes']) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Capacidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['ambientes'] as $id => $ambiente): ?>
                    <tr>
                        <td><?= $ambiente['nome'] ?></td>
                        <td><?= $ambiente['capacidade'] ?> pessoas</td>
                        <td class="<?= $ambiente['status'] === 'Livre' ? 'status-livre' : 'status-ocupado' ?>">
                            <?= $ambiente['status'] ?>
                        </td>
                        <td>
                            <a href="gestor_ambiente.php?acao=status&id=<?= $id ?>" class="btn-acao btn-status">Mudar Status</a>
                            <a href="gestor_ambiente.php?acao=excluir&id=<?= $id ?>" class="btn-acao btn-excluir" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum ambiente cadastrado ainda.</p>
    <?php endif; ?>

</div>

</body>
</html>