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
    <title>SGM - Gestão de Locais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .list-group-item:hover { background-color: #f8f9fa; }
        .card { transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="gestor_dashboard.php">SGM Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="gestor_chamados.php">Chamados</a>
                <a class="nav-link active" href="gestor_locais.php">Locais</a>
                <a class="nav-link" href="api/logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestão de Locais</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBloco">
                <i class="bi bi-plus-circle"></i> Novo Bloco
            </button>
        </div>

        <div class="row" id="containerBlocos">
            <!-- Blocos serão carregados aqui -->
        </div>
    </div>

    <!-- Modal Bloco -->
    <div class="modal fade" id="modalBloco" tabindex="-1">
        <div class="modal-dialog">
            <form id="formBloco" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Bloco</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="blocoId">
                    <div class="mb-3">
                        <label class="form-label">Nome do Bloco</label>
                        <input type="text" id="blocoNome" class="form-control" required placeholder="Ex: Bloco A">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ambiente -->
    <div class="modal fade" id="modalAmbiente" tabindex="-1">
        <div class="modal-dialog">
            <form id="formAmbiente" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Ambiente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ambienteId">
                    <input type="hidden" id="ambienteBlocoId">
                    <div class="mb-3">
                        <label class="form-label">Nome do Ambiente</label>
                        <input type="text" id="ambienteNome" class="form-control" required placeholder="Ex: Sala 101">
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
        async function carregarTudo() {
            const res = await fetch('api/localizacoes.php?acao=listar_blocos');
            const blocos = await res.json();
            const container = document.getElementById('containerBlocos');
            container.innerHTML = '';

            for (const bloco of blocos) {
                const resAmb = await fetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${bloco.id_bloco}`);
                const ambientes = await resAmb.json();

                const card = `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <strong>${bloco.nome}</strong>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="novoAmbiente(${bloco.id_bloco})"><i class="bi bi-plus"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="excluirBloco(${bloco.id_bloco})"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush">
                                ${ambientes.map(a => `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        ${a.nome}
                                        <button class="btn btn-sm text-danger" onclick="excluirAmbiente(${a.id_ambiente})"><i class="bi bi-x"></i></button>
                                    </li>
                                `).join('') || '<li class="list-group-item text-muted">Nenhum ambiente</li>'}
                            </ul>
                        </div>
                    </div>
                `;
                container.innerHTML += card;
            }
        }

        function novoAmbiente(id_bloco) {
            document.getElementById('ambienteBlocoId').value = id_bloco;
            document.getElementById('ambienteNome').value = '';
            new bootstrap.Modal(document.getElementById('modalAmbiente')).show();
        }

        document.getElementById('formBloco').onsubmit = async (e) => {
            e.preventDefault();
            const nome = document.getElementById('blocoNome').value;
            const res = await fetch('api/localizacoes.php?acao=salvar_bloco', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ nome: nome })
            });
            const result = await res.json();
            if(result.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalBloco')).hide();
                carregarTudo();
            }
        };

        document.getElementById('formAmbiente').onsubmit = async (e) => {
            e.preventDefault();
            const nome = document.getElementById('ambienteNome').value;
            const id_bloco = document.getElementById('ambienteBlocoId').value;
            const res = await fetch('api/localizacoes.php?acao=salvar_ambiente', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ nome: nome, id_bloco: id_bloco })
            });
            const result = await res.json();
            if(result.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalAmbiente')).hide();
                carregarTudo();
            }
        };

        async function excluirBloco(id) {
            if(!confirm("Excluir o bloco e todos os seus ambientes?")) return;
            const res = await fetch(`api/localizacoes.php?acao=excluir_bloco&id=${id}`, { method: 'DELETE' });
            if((await res.json()).success) carregarTudo();
        }

        async function excluirAmbiente(id) {
            if(!confirm("Excluir este ambiente?")) return;
            const res = await fetch(`api/localizacoes.php?acao=excluir_ambiente&id=${id}`, { method: 'DELETE' });
            if((await res.json()).success) carregarTudo();
        }

        carregarTudo();
    </script>
</body>
</html>