<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGM - Painel do Gestor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">SGM | Gestão Administrativa</span>
            <div class="text-white">
                <span>Olá, <?= $_SESSION['user_nome'] ?></span> | 
                <a href="api/logout.php" class="btn btn-sm btn-outline-light">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title">Novas Solicitações</h5>
                        <h2 id="qtdAbertos">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning shadow">
                    <div class="card-body">
                        <h5 class="card-title">Em Atendimento</h5>
                        <h2 id="qtdExecucao">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger shadow">
                    <div class="card-body">
                        <h5 class="card-title">Críticos / Urgentes</h5>
                        <h2 id="qtdUrgentes">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center mt-4">
                <a href="gestor_chamados.php" class="btn btn-lg btn-secondary">
                    <i class="bi bi-list-task"></i> Gerenciar Todos os Chamados
                </a>
                <a href="gestor_locais.php" class="btn btn-lg btn-outline-primary ms-2">
                    <i class="bi bi-geo-alt"></i> Configurar Ambientes
                </a>
            </div>
        </div>
    </div>

    <script>
        async function atualizarResumo() {
            const res = await fetch('api/dashboard_gestor.php');
            const dados = await res.json();
            
            document.getElementById('qtdAbertos').innerText = dados.abertos || 0;
            document.getElementById('qtdExecucao').innerText = dados.em_execucao || 0;
            document.getElementById('qtdUrgentes').innerText = dados.urgentes || 0;
        }

        atualizarResumo();
        // Atualiza a cada 30 segundos
        setInterval(atualizarResumo, 30000);
    </script>
</body>
</html>