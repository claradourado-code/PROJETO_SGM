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
    <title>SGM - Detalhes do Chamado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .thumb-img { width: 100%; height: 100px; cursor: pointer; object-fit: cover; border-radius: 4px; transition: 0.3s; }
        .thumb-img:hover { opacity: 0.8; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <a href="gestor_chamados.php" class="btn btn-outline-secondary mb-3">Voltar</a>
        <div class="row">
            <div class="col-md-7">
                <div class="card shadow mb-4">
                    <div class="card-header bg-white"><strong>Dados da Solicitação</strong></div>
                    <div id="detalhesChamado" class="card-body">Carregando...</div>
                </div>
                <div id="areaFechamento"></div>
            </div>
            <div class="col-md-5">
                <div class="card shadow border-primary">
                    <div class="card-header bg-primary text-white"><strong>Triagem e Atribuição</strong></div>
                    <div class="card-body">
                        <form id="formAtribuir">
                            <input type="hidden" id="id_chamado" value="<?= $id ?>">
                            <div class="mb-3">
                                <label>Técnico Responsável</label>
                                <select id="selectTecnico" class="form-select" required>
                                    
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Prioridade</label>
                                <select id="prioridade" class="form-select">
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Data Prevista</label>
                                <input type="date" id="data_prevista" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Confirmar Atribuição</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-body p-0 text-center">
                    <img src="" id="imgModal" class="img-fluid">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        async function carregarDados() {
            // Carrega Técnicos
            const resTec = await fetch('api/usuarios.php');
            const tecnicos = await resTec.json();
            const select = document.getElementById('selectTecnico');
            select.innerHTML = '<option value="">Selecione um técnico...</option>';
            tecnicos.forEach(t => select.innerHTML += `<option value="${t.id_usuario}">${t.nome}</option>`);

            // Carrega Chamado
            const c = await (await fetch(`api/chamados.php?id=<?= $id ?>`)).json();
            document.getElementById('detalhesChamado').innerHTML = `
                <p><strong>Status:</strong> <span class="badge bg-secondary">${c.status.toUpperCase()}</span></p>
                <p><strong>Descrição:</strong> ${c.descricao_problema}</p>
                <p><strong>Local:</strong> ${c.bloco_nome} - ${c.ambiente_nome}</p>
                <p><strong>Solicitante:</strong> ${c.solicitante_nome}</p>
                <p><strong>Abertura:</strong> ${new Date(c.data_abertura).toLocaleString()}</p>
                <div id="fotosContainer"></div>
            `;

            if(c.id_tecnico) document.getElementById('selectTecnico').value = c.id_tecnico;
            if(c.prioridade) document.getElementById('prioridade').value = c.prioridade;
            if(c.data_previsao_conclusao) document.getElementById('data_prevista').value = c.data_previsao_conclusao;

            // Carrega Fotos
            const anexos = await (await fetch(`api/anexos.php?id_chamado=<?= $id ?>`)).json();
            if(anexos.length > 0) {
                let htmlFotos = '<hr><h6>Evidências:</h6><div class="row">';
                anexos.forEach(arq => {
                    htmlFotos += `
                        <div class="col-4 text-center mb-2">
                            <img src="${arq.caminho_arquivo}" class="thumb-img" onclick="verFoto('${arq.caminho_arquivo}')">
                            <small class="text-muted">${arq.tipo_anexo === 'abertura' ? 'Abertura' : 'Conclusão'}</small>
                        </div>`;
                });
                document.getElementById('fotosContainer').innerHTML = htmlFotos + '</div>';
            }

            // Botões de Status
            const area = document.getElementById('areaFechamento');
            if (c.status === 'concluido') {
                area.innerHTML = `<div class="alert alert-success">
                    <h6>Técnico finalizou:</h6><p>${c.solucao_tecnica || 'Sem descrição'}</p>
                    <button onclick="alterarStatusOS(<?= $id ?>, 'fechar')" class="btn btn-success w-100">Fechar O.S.</button>
                </div>`;
            } else if (c.status === 'fechado') {
                area.innerHTML = `<button onclick="alterarStatusOS(<?= $id ?>, 'reabrir')" class="btn btn-warning w-100">Reabrir Chamado</button>`;
            }
        }

        async function alterarStatusOS(id, acao) {
            if(!confirm("Confirmar alteração de status?")) return;
            const res = await fetch('api/gestor_acoes.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id_chamado: id, acao: acao })
            });
            if((await res.json()).success) location.reload();
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
            if((await res.json()).success) window.location.href = 'gestor_chamados.php';
        };

        carregarDados();
    </script>
</body>
</html>