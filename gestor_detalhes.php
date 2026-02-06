<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Solicitações SGM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
        
        .card-custom {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: none;
            padding: 20px;
        }

        /* Estilo da Tabela */
        .table-custom th { 
            background-color: #f8f9fa; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            color: #6c757d; 
            font-weight: 700;
            vertical-align: middle;
        }
        .table-custom td { vertical-align: middle; font-size: 0.95rem; }

        /* Badge de Status com a Bolinha */
        .badge-status {
            padding: 6px 10px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .bg-aberto { background-color: #ffe5e5; color: #d63384; }
        .bg-execucao { background-color: #fff3cd; color: #856404; }
        .bg-concluido { background-color: #d1e7dd; color: #0f5132; }

        /* Coluna de Evidência */
        .btn-evidencia {
            color: #0d6efd;
            background: #e7f1ff;
            border: none;
            padding: 5px 10px;
            border-radius: 6px;
            transition: 0.2s;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .btn-evidencia:hover { background: #0d6efd; color: white; }

        /* Coluna Triagem */
        .select-tecnico {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 4px;
            font-size: 0.9rem;
            width: 130px;
        }

        /* Botões de Ação */
        .btn-action-sm {
            padding: 4px 8px;
            font-size: 0.8rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }
        .btn-confirm { background-color: #198754; color: white; } /* Verde */
        .btn-reopen { background-color: #fd7e14; color: white; } /* Laranja */
        .btn-confirm:hover { background-color: #157347; }
        .btn-reopen:hover { background-color: #e96b02; }

        .meta-info { font-size: 0.75rem; color: #aaa; display: block; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="card-custom">
        <h4 class="mb-4"><i class="bi bi-list-task"></i> Dados da Saolicitação</h4>

        <div class="table-responsive">
            <table class="table table-hover table-custom">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Solicitante / Local</th>
                        <th width="25%">Descrição</th>
                        <th width="10%">Abertura</th>
                        <th width="8%">Evidência</th>
                        <th width="12%">Status</th>
                        <th width="15%">Triagem e Atribuição</th>
                        <th width="10%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SIMULAÇÃO DE DADOS VINDO DO BANCO
                    $solicitacoes = [
                        [
                            'id' => 105,
                            'solicitante' => 'Maria Silva',
                            'local' => 'Financeiro - Sala 2',
                            'desc' => 'Impressora soltando fumaça e fazendo barulho estranho.',
                            'data' => '06/02 08:30',
                            'imagem' => 'erro_imp.jpg',
                            'status' => 'aberto',
                            'tecnico_atual' => '' 
                        ],
                        [
                            'id' => 104,
                            'solicitante' => 'João Souza',
                            'local' => 'Recepção',
                            'desc' => 'Monitor não liga, cabo parece estar com mal contato.',
                            'data' => '05/02 14:15',
                            'imagem' => '', // Sem imagem
                            'status' => 'execucao',
                            'tecnico_atual' => 'robson'
                        ],
                        [
                            'id' => 102,
                            'solicitante' => 'Ana Paula',
                            'local' => 'RH',
                            'desc' => 'Instalação do pacote Office no computador novo.',
                            'data' => '04/02 09:00',
                            'imagem' => 'print.png',
                            'status' => 'concluido',
                            'tecnico_atual' => 'robson'
                        ]
                    ];

                    // Lista de Técnicos para o Select
                    $tecnicos = ['robson' => 'Robson (N1)', 'elias' => 'Elias (N2)', 'suporte' => 'Externo'];

                    foreach ($solicitacoes as $s) {
                        // Definição de cores e textos baseados no status
                        $statusClass = '';
                        $statusText = '';
                        
                        switch($s['status']) {
                            case 'aberto': 
                                $statusClass = 'bg-aberto'; $statusText = 'Aberto'; break;
                            case 'execucao': 
                                $statusClass = 'bg-execucao'; $statusText = 'Em andamento'; break;
                            case 'concluido': 
                                $statusClass = 'bg-concluido'; $statusText = 'Concluído'; break;
                        }
                    ?>
                    <tr>
                        <td><strong>#<?= $s['id'] ?></strong></td>

                        <td>
                            <div style="font-weight: 600;"><?= $s['solicitante'] ?></div>
                            <span class="meta-info"><i class="bi bi-geo-alt"></i> <?= $s['local'] ?></span>
                        </td>

                        <td><?= $s['desc'] ?></td>

                        <td><?= $s['data'] ?></td>

                        <td class="text-center">
                            <?php if($s['imagem']): ?>
                                <a href="#" class="btn-evidencia" title="Ver imagem">
                                    <i class="bi bi-image"></i> Ver
                                </a>
                            <?php else: ?>
                                <span class="text-muted" style="font-size: 0.8rem;">--</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="badge-status <?= $statusClass ?>">
                                <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                <?= $statusText ?>
                            </span>
                        </td>

                        <td>
                            <?php if($s['status'] !== 'concluido'): ?>
                                <form style="display: flex; align-items: center;">
                                    <select class="select-tecnico">
                                        <option value="">-- Selecione --</option>
                                        <?php foreach($tecnicos as $val => $nome): ?>
                                            <option value="<?= $val ?>" <?= ($s['tecnico_atual'] == $val) ? 'selected' : '' ?>>
                                                <?= $nome ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn-action-sm btn-confirm" title="Confirmar Atribuição">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Atendido por: <strong><?= $tecnicos[$s['tecnico_atual']] ?? 'Desconhecido' ?></strong></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if($s['status'] == 'concluido'): ?>
                                <button class="btn-action-sm btn-reopen">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reabrir
                                </button>
                            <?php else: ?>
                                <span class="text-muted" style="font-size: 0.8rem;">--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>