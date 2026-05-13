<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    header("Location: login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGM - Meus Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .mini-thumb { width: 40px; height: 40px; object-fit: cover; cursor: pointer; border-radius: 4px; border: 1px solid #ddd; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">SGM - Painel do Solicitante</span>
            <span class="text-white">Olá, <?= $_SESSION['user_nome'] ?> | <a href="api/logout.php" class="text-white">Sair</a></span>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h2>Minhas Solicitações</h2>
            <a href="solicitante_abrir_chamado.php" class="btn btn-success">+ Nova Solicitação</a>
        </div>
        <div class="card shadow">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Local</th>
                            <th>Descrição</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaChamados"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-body p-0 text-center">
                    <img src="" id="imgModal" class="img-fluid">
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

        async function carregarChamados() {
            const chamados = await (await fetch('api/chamados.php')).json();
            const lista = document.getElementById('tabelaChamados');
            const cores = { 'aberto': 'bg-secondary', 'agendado': 'bg-info', 'em_execucao': 'bg-warning', 'concluido': 'bg-success', 'fechado': 'bg-dark' };

            lista.innerHTML = await Promise.all(chamados.map(async c => {
                // Busca se tem foto para mostrar miniatura na lista
                const anexos = await (await fetch(`api/anexos.php?id_chamado=${c.id_chamado}`)).json();
                const thumbHtml = anexos.length > 0 ? 
                    `<img src="${anexos[0].caminho_arquivo}" class="mini-thumb" onclick="verFoto('${anexos[0].caminho_arquivo}')">` : 
                    '<i class="bi bi-image text-muted"></i>';

                return `<tr>
                    <td>#${c.id_chamado}</td>
                    <td>${thumbHtml}</td>
                    <td>${c.bloco_nome} - ${c.ambiente_nome}</td>
                    <td>${c.descricao_problema.substring(0,30)}...</td>
                    <td>${new Date(c.data_abertura).toLocaleDateString()}</td>
                    <td><span class="badge ${cores[c.status]}">${c.status.toUpperCase()}</span></td>
                </tr>`;
            })).then(rows => rows.join(''));
        }
        carregarChamados();
    </script>
</body>
</html>