<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header("Location: login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGM - Minha Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark  mb-4" style="background-color: #ff0055;">
        <div class="container">
            <span class="navbar-brand">SGM Técnico | <?= $_SESSION['user_nome'] ?></span>
            <a href="api/logout.php" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-3">Minha Fila de Trabalho</h4>
        
        <div id="listaTarefas" class="row g-3">
            </div>
    </div>

    <script>
        const coresPrioridade = { 'urgente': 'danger', 'alta': 'warning', 'media': 'primary', 'baixa': 'secondary' };

        async function carregarTarefas() {
            const res = await fetch('api/tecnico_tarefas.php');
            const tarefas = await res.json();
            const container = document.getElementById('listaTarefas');

            if (tarefas.length === 0) {
                container.innerHTML = '<div class="col-12 text-center text-muted mt-5"><h5>Nenhuma tarefa pendente! 🎉</h5></div>';
                return;
            }

            container.innerHTML = tarefas.map(t => `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-start border-4 border-${coresPrioridade[t.prioridade]}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-${coresStatus(t.status)}">${t.status.replace('_', ' ').toUpperCase()}</span>
                                <small class="text-muted">#${t.id_chamado}</small>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted">${t.bloco_nome} - ${t.ambiente_nome}</h6>
                            <p class="card-text text-truncate" style="max-height: 50px;">${t.descricao_problema}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-danger fw-bold"><i class="bi bi-calendar-event"></i> ${t.data_previsao_conclusao ? new Date(t.data_previsao_conclusao).toLocaleDateString() : 'Sem prazo'}</small>
                                <a href="tecnico_atendimento.php?id=${t.id_chamado}" class="btn btn-sm btn-success">Atender</a>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function coresStatus(status) {
            const map = { 'agendado': 'info', 'em_execucao': 'warning', 'concluido': 'success' };
            return map[status] || 'secondary';
        }

        carregarTarefas();
    </script>
</body>
</html>