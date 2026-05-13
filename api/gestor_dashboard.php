<?php
// 1. Iniciar a sessão (SEMPRE a primeira coisa no arquivo)
session_start();

// 2. Proteção de Rota: Verificar se o usuário está logado e é um gestor
// (Assumindo que você salva isso na sessão durante o login)
if (!isset($_SESSION['usuario_logado']) || $_SESSION['perfil'] !== 'gestor') {
    // Se não for gestor, chuta de volta pra tela de login
    header("Location: login.php?erro=acesso_negado");
    exit();
}

// 3. Simulação de busca no Banco de Dados (Substitua pela sua conexão real)
$nomeGestor = $_SESSION['nome_usuario'] ?? 'Gestor';
$totalUsuarios = 150; 
$vendasHoje = "R$ 4.500,00";
$alertas = 3;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Gestor</title>
    <style>
        /* CSS Básico para organizar a tela */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; }
        .sidebar { width: 250px; background: #2c3e50; color: white; position: fixed; height: 100%; padding: 20px 0; }
        .sidebar a { color: white; text-decoration: none; padding: 15px 20px; display: block; }
        .sidebar a:hover { background: #1a252f; }
        .content { margin-left: 250px; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .cards { display: flex; gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; }
        .card h3 { margin: 0 0 10px 0; color: #7f8c8d; font-size: 14px; }
        .card p { margin: 0; font-size: 24px; font-weight: bold; color: #2c3e50; }
        .btn-logout { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="text-align: center; margin-bottom: 30px;">Meu App</h2>
        <a href="gestor_dashboard.php">Dashboard</a>
        <a href="gerenciar_usuarios.php">Usuários</a>
        <a href="relatorios.php">Relatórios</a>
        <a href="configuracoes.php">Configurações</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Bem-vindo, <?php echo htmlspecialchars($nomeGestor); ?>!</h2>
            <a href="logout.php" class="btn-logout">Sair</a>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Total de Usuários</h3>
                <p><?php echo $totalUsuarios; ?></p>
            </div>
            <div class="card">
                <h3>Vendas de Hoje</h3>
                <p><?php echo $vendasHoje; ?></p>
            </div>
            <div class="card">
                <h3>Alertas Pendentes</h3>
                <p style="color: #e74c3c;"><?php echo $alertas; ?></p>
            </div>
        </div>

        <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 8px;">
            <h3>Últimas Atividades</h3>
            <p>Nenhuma atividade recente encontrada.</p>
        </div>
    </div>

</body>
</html>