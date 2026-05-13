<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGM - Gestão de Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .card-service { transition: 0.3s; border: none; border-radius: 12px; }
        .card-service:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.1); transform: translateY(-3px); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="gestor_dashboard.php">SGM Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="gestor_chamados.php">Chamados</a>
                <a class="nav-link" href="gestor_locais.php">Locais</a>
                <a class="nav-link active" href="gestor_servicos.php">Serviços</a>
                <a class="nav-link" href="api/logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Tipos de Serviço</h2>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="bi bi-plus-lg"></i> Novo Serviço
            </button>
        </div>

        <div class="row" id="listaServicos">
            <!-- Serviços via AJAX -->
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalServico" tabindex="-1">
        <div class="modal-dialog">
            <form id="formServico" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Serviço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_tipo">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" id="nome" class="form-control" required placeholder="Ex: Elétrica">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea id="descricao" class="form-control" rows="3" placeholder="Opcional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('modalServico'));

        async function carregarServicos() {
            const res = await fetch('api/tipos_servico.php?acao=listar');
            const dados = await res.json();
            const container = document.getElementById('listaServicos');
            
            container.innerHTML = dados.map(s => `
                <div class="col-md-4 mb-3">
                    <div class="card card-service shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary">${s.nome}</h5>
                            <p class="card-text text-muted small">${s.descricao || 'Sem descrição'}</p>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary" onclick='editar(${JSON.stringify(s)})'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="excluir(${s.id_tipo})"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function abrirModal() {
            document.getElementById('formServico').reset();
            document.getElementById('id_tipo').value = '';
            document.getElementById('modalTitle').innerText = 'Novo Serviço';
            modal.show();
        }

        function editar(s) {
            document.getElementById('id_tipo').value = s.id_tipo;
            document.getElementById('nome').value = s.nome;
            document.getElementById('descricao').value = s.descricao;
            document.getElementById('modalTitle').innerText = 'Editar Serviço';
            modal.show();
        }

        document.getElementById('formServico').onsubmit = async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('id_tipo').value,
                nome: document.getElementById('nome').value,
                descricao: document.getElementById('descricao').value
            };
            const res = await fetch('api/tipos_servico.php?acao=salvar', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            if((await res.json()).success) {
                modal.hide();
                carregarServicos();
            }
        };

        async function excluir(id) {
            if(!confirm("Deseja realmente excluir este tipo de serviço?")) return;
            const res = await fetch(`api/tipos_servico.php?acao=excluir&id=${id}`, { method: 'DELETE' });
            if((await res.json()).success) carregarServicos();
        }

        carregarServicos();
    </script>
</body>
</html>