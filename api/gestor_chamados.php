<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-chamado { margin-bottom: 15px; border-left: 5px solid #0d6efd; }
        .header-bg { background-color: #343a40; color: white; padding: 20px 0; margin-bottom: 30px; }
    </style>
</head>
<body class="bg-light">

    <nav class="header-bg text-center">
        <h2>🔧 Sistema Help Desk</h2>
    </nav>

    <div class="container">
        
        <?= $mensagem ?>

        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Novo Chamado</h5>
                    </div>
                    <div class="card-body">
                        <form action="gestor_chamados.php" method="POST">
                            <div class="mb-3">
                                <label>Título</label>
                                <input type="text" name="titulo" class="form-control" placeholder="Ex: Impressora sem tinta" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Categoria</label>
                                <select name="categoria" class="form-select">
                                    <option>Hardware</option>
                                    <option>Software</option>
                                    <option>Rede</option>
                                    <option>Outros</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva o problema..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Abrir Chamado</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <h4 class="mb-3">Lista de Chamados</h4>
                
                <?php if(empty($chamados)): ?>
                    <div class="alert alert-warning">Nenhum chamado encontrado.</div>
                <?php else: ?>
                    
                    <?php foreach($chamados as $chamado): ?>
                        <?php if(count($chamado) < 3) continue; // Pula linhas inválidas ?>
                        <div class="card card-chamado shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($chamado[0]) ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">Categoria: <?= htmlspecialchars($chamado[1]) ?></h6>
                                <p class="card-text"><?= htmlspecialchars($chamado[2]) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>