<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header("Location: login.php"); exit;
}
$id = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Execução de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .service-action-btn { height: 70px; font-size: 1.2rem; font-weight: bold; border-radius: 15px; }
        .info-label { font-size: 0.8rem; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark mb-4" style="background: #2b2d42 !important;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="tecnico_minhas_tarefas.php">SGM TÉCNICO</a>
            <a href="tecnico_minhas_tarefas.php" class="btn btn-sm btn-outline-light border-0"><i class="bi bi-chevron-left me-1"></i> Voltar</a>
        </div>
    </nav>

    <div class="container animate-fade-in pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="glass-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Tarefa #<?= $id ?></h4>
                        <span id="badgeStatus" class="badge bg-secondary px-3 py-2">CARREGANDO...</span>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <div class="info-label">Local</div>
                            <div id="infoLocal" class="fw-bold fs-5">...</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="info-label">Prioridade</div>
                            <div id="infoPrioridade" class="fw-bold fs-5">...</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Descrição do Problema</div>
                            <p id="infoDescricao" class="bg-white p-3 rounded-3 shadow-sm mt-1 mb-0 border">...</p>
                        </div>
                    </div>

                    <div id="containerFotos" class="row g-2 mb-4"></div>

                    <!-- Fluxo de Ações -->
                    <div id="fluxoAcoes" class="mt-5">
                        <button id="btnIniciar" onclick="alterarStatus('iniciar')" class="btn btn-primary w-100 service-action-btn shadow d-none">
                            INICIAR ATENDIMENTO <i class="bi bi-play-circle-fill ms-2"></i>
                        </button>

                        <div id="formConclusao" class="d-none animate-fade-in">
                            <h5 class="fw-bold mb-3">Relatório de Conclusão</h5>
                            <form id="formFinalizar">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Solução Aplicada</label>
                                    <textarea id="solucao" class="form-control border-0 shadow-sm" rows="4" required placeholder="O que foi feito para resolver?"></textarea>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tempo Gasto (min)</label>
                                        <input type="number" id="tempo" class="form-control border-0 shadow-sm" placeholder="Ex: 45" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Foto da Solução</label>
                                        <input type="file" id="foto" class="form-control border-0 shadow-sm" accept="image/*">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success w-100 service-action-btn shadow">
                                    CONCLUIR CHAMADO <i class="bi bi-check-all ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function carregarDados() {
            const res = await fetch(`api/chamados.php?id=<?= $id ?>`);
            const c = await res.json();

            document.getElementById('infoLocal').innerText = `${c.bloco_nome} - ${c.ambiente_nome}`;
            document.getElementById('infoPrioridade').innerText = c.prioridade.toUpperCase();
            document.getElementById('infoPrioridade').className = `fw-bold fs-5 ${obterCorPrioridade(c.prioridade)}`;
            document.getElementById('infoDescricao').innerText = c.descricao_problema;
            
            const badge = document.getElementById('badgeStatus');
            badge.innerText = c.status.replace('_', ' ').toUpperCase();
            badge.className = `badge px-3 py-2 ${obterBadgeClass(c.status)}`;

            // Controlar visibilidade de botões
            if(c.status === 'agendado') {
                document.getElementById('btnIniciar').classList.remove('d-none');
            } else if(c.status === 'em_execucao') {
                document.getElementById('formConclusao').classList.remove('d-none');
            }

            // Fotos
            const resFotos = await fetch(`api/anexos.php?id_chamado=<?= $id ?>`);
            const fotos = await resFotos.json();
            const container = document.getElementById('containerFotos');
            if(fotos.length > 0) {
                container.innerHTML = '<div class="col-12"><small class="info-label">Fotos da Abertura</small></div>';
                fotos.forEach(f => {
                    if(f.tipo_anexo === 'abertura') {
                        container.innerHTML += `<div class="col-4"><img src="${f.caminho_arquivo}" class="img-fluid rounded-3 border" style="height: 100px; width: 100%; object-fit: cover;"></div>`;
                    }
                });
            }
        }

        async function alterarStatus(acao) {
            const res = await fetch('api/tecnico_acoes.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id_chamado: <?= $id ?>, acao: acao })
            });
            if((await res.json()).success) location.reload();
        }

        document.getElementById('formFinalizar').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData();
            formData.append('id_chamado', <?= $id ?>);
            formData.append('acao', 'concluir');
            formData.append('solucao', document.getElementById('solucao').value);
            formData.append('tempo', document.getElementById('tempo').value);
            const fotoFile = document.getElementById('foto').files[0];
            if(fotoFile) formData.append('foto', fotoFile);

            const res = await fetch('api/tecnico_acoes.php', { method: 'POST', body: formData });
            const result = await res.json();
            if(result.success) {
                alert("Serviço concluído com sucesso!");
                location.href = 'tecnico_minhas_tarefas.php';
            } else {
                alert("Erro: " + result.message);
            }
        };

        function obterBadgeClass(s) {
            const map = { 'agendado': 'bg-info', 'em_execucao': 'bg-warning text-dark', 'concluido': 'bg-success' };
            return map[s] || 'bg-secondary';
        }

        function obterCorPrioridade(p) {
            const map = { 'urgente': 'text-danger', 'alta': 'text-warning', 'media': 'text-primary', 'baixa': 'text-secondary' };
            return map[p] || '';
        }

        carregarDados();
    </script>
</body>
</html>