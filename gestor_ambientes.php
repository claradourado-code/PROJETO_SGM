<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Ambientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gerenciar Ambientes</h2>
        <a href="cadastro.html" class="btn btn-success">+ Novo Ambiente</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome do Ambiente</th>
                        <th>Capacidade</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Sala de Reuniões A</td>
                        <td>10 pessoas</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary">Editar</button>
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Laboratório de Informática</td>
                        <td>30 pessoas</td>
                        <td><span class="badge bg-warning text-dark">Manutenção</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary">Editar</button>
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>