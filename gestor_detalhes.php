<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php"); exit;
}
$id = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Gerenciar Chamado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .thumb-img { width: 100%; height: 120px; object-fit: cover; border-radius: 12px; cursor: pointer; transition: 0.3s; border: 2px solid white; }
        .thumb-img:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .status-timeline { border-left: 2px solid #e9ecef; margin-left: 10px; padding-left: 20px; position: relative; }
        .status-timeline::before { content: ''; position: absolute; left: -7px; top: 0; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="gestor_dashboard.php">SGM ADMIN</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="gestor_chamados.php"><i class="bi bi-chevron-left me-1"></i> Voltar</a>
            </div>
        </div>
    </nav>

    <div class="container animate-fade-in">
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Informações Principais -->
                <div class="glass-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="fw-bold mb-0">Chamado #<?= $id ?></h4>
                        <span id="badgeStatus" class="badge bg-secondary px-3 py-2">CARREGANDO...</span>
                    </div>
                    <hr>
                    <div id="detalhesContent">
                        <div class="placeholder-glow">
                            <span class="placeholder col-7"></span>
                            <span class="placeholder col-4"></span>
                            <span class="placeholder col-4"></span>
                            <span class="placeholder col-6"></span>
                        </div>
                    </div>
                    <div id="fotosContainer" class="row mt-4"></div>
                </div>

                <!-- Histórico / Solução se houver -->
                <div id="areaSolucao" class="d-none">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 bg-success bg-opacity-10 rounded-4">
                            <h5 class="fw-bold text-success mb-3"><i class="bi bi-check-circle-fill me-2"></i>Solução Técnica</h5>
                            <p id="solucaoTexto" class="mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Triagem -->
                <div class="glass-card p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-4">Ações de Gestão</h5>
                    <form id="formAtribuir">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Atribuir Técnico</label>
                            <select id="selectTecnico" class="form-select border-0 shadow-sm bg-white" required>
                                <option value="">Carregando técnicos...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Definir Prioridade</label>
                            <select id="prioridade" class="form-select border-0 shadow-sm bg-white">
                                <option value="baixa">Baixa</option>
                                <option value="media">Média</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Prazo de Conclusão</label>
                            <input type="date" id="data_prevista" class="form-control border-0 shadow-sm bg-white" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                            Atualizar Chamado
                        </button>
                    </form>
                    
                    <div id="areaAcoesExtras" class="d-none mt-3">
                        <hr>
                        <button id="btnFechar" onclick="alterarStatusOS('fechar')" class="btn btn-success w-100 py-3">Finalizar & Arquivar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto -->
    <div class="modal fade" id="modalFoto" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <img src="" id="imgModal" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function carregarDados() {
            // 1. Técnicos
            const resTec = await fetch('api/usuarios.php?perfil=tecnico');
            const tecnicos = await resTec.json();
            const selT = document.getElementById('selectTecnico');
            selT.innerHTML = '<option value="">Selecione um técnico...</option>';
            tecnicos.forEach(t => selT.innerHTML += `<option value="${t.id_usuario}">${t.nome}</option>`);

            // 2. Chamado
            const c = await (await fetch(`api/chamados.php?id=<?= $id ?>`)).json();
            
            const badge = document.getElementById('badgeStatus');
            badge.innerText = c.status.replace('_', ' ').toUpperCase();
            badge.className = `badge px-3 py-2 ${obterBadgeClass(c.status)}`;

            document.getElementById('detalhesContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Solicitante</small>
                        <span class="fw-bold">${c.solicitante_nome}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Localização</small>
                        <span class="fw-bold">${c.bloco_nome} - ${c.ambiente_nome}</span>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted d-block">Descrição do Problema</small>
                        <p class="fs-5">${c.descricao_problema}</p>
                    </div>
                </div>
            `;

            if(c.id_tecnico) document.getElementById('selectTecnico').value = c.id_tecnico;
            if(c.prioridade) document.getElementById('prioridade').value = c.prioridade;
            if(c.data_previsao_conclusao) document.getElementById('data_prevista').value = c.data_previsao_conclusao;

            if(c.status === 'concluido') {
                document.getElementById('areaSolucao').classList.remove('d-none');
                document.getElementById('solucaoTexto').innerText = c.solucao_tecnica;
                document.getElementById('areaAcoesExtras').classList.remove('d-none');
            }

            // 3. Fotos
            const anexos = await (await fetch(`api/anexos.php?id_chamado=<?= $id ?>`)).json();
            const container = document.getElementById('fotosContainer');
            if(anexos.length > 0) {
                container.innerHTML = '<div class="col-12"><hr><h6 class="fw-bold mb-3">Evidências Fotográficas</h6></div>';
                anexos.forEach(a => {
                    container.innerHTML += `
                        <div class="col-4 col-md-3 mb-3">
                            <img src="${a.caminho_arquivo}" class="thumb-img" onclick="verFoto('${a.caminho_arquivo}')">
                            <div class="text-center small mt-1 text-muted">${a.tipo_anexo === 'abertura' ? 'Abertura' : 'Fechamento'}</div>
                        </div>`;
                });
            }
        }

        function obterBadgeClass(status) {
            const map = { 'aberto': 'bg-secondary', 'em_execucao': 'bg-warning text-dark', 'concluido': 'bg-success', 'fechado': 'bg-dark' };
            return map[status] || 'bg-secondary';
        }

        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        document.getElementById('formAtribuir').onsubmit = async (e) => {
            e.preventDefault();
            const res = await fetch('api/atribuir_chamado.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id_chamado: <?= $id ?>,
                    id_tecnico: document.getElementById('selectTecnico').value,
                    prioridade: document.getElementById('prioridade').value,
                    data_prevista: document.getElementById('data_prevista').value
                })
            });
            if((await res.json()).success) {
                alert("Chamado atualizado com sucesso!");
                location.reload();
            }
        };

        async function alterarStatusOS(acao) {
            if(!confirm("Deseja confirmar esta ação?")) return;
            const res = await fetch('api/gestor_acoes.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id_chamado: <?= $id ?>, acao: acao })
            });
            if((await res.json()).success) location.href = 'gestor_chamados.php';
        }

        carregarDados();
    </script>
</body>
</html>