<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin SGM - Gestão de Chamados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        h2 { color: #2c3e50; margin: 0; }

        /* --- BARRA DE FILTROS (Botões) --- */
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .filter-btn {
            border: none;
            background: #e9ecef;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            color: #555;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn:hover { background: #dde2e6; }
        
        /* Estilos Ativos para cada filtro */
        .filter-btn.active { color: white; transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .filter-btn.active[data-filter="todos"] { background: #333; }
        .filter-btn.active[data-filter="aberto"] { background: #dc3545; } /* Vermelho */
        .filter-btn.active[data-filter="execucao"] { background: #fd7e14; } /* Laranja */
        .filter-btn.active[data-filter="concluido"] { background: #28a745; } /* Verde */

        /* Contadores nos botões */
        .badge-count {
            background: rgba(255,255,255,0.3);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8em;
        }

        /* --- TABELA --- */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85em; }
        tr { transition: background 0.2s; }
        tr:hover { background-color: #fbfbfb; }

        /* Cores dos Status na Tabela */
        .status-badge { padding: 5px 12px; border-radius: 12px; font-size: 0.85em; font-weight: bold; }
        .st-aberto { background: #f8d7da; color: #721c24; }
        .st-execucao { background: #fff3cd; color: #856404; }
        .st-concluido { background: #d4edda; color: #155724; }

        /* Botão de Ação */
        .btn-action {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            color: #555;
        }
        .btn-action:hover { background: #007bff; color: white; border-color: #007bff; }
        
    </style>
</head>
<body>


<div class="admin-container">
    <div class="header">
        <h2><i class="fas fa-tools"></i> SGM - Todos os Chamados</h2>
        <div style="color: #b6a5a5ff;">Usuário: <strong> SGM Admin</strong></div>
        
    </div>

    <div class="filter-bar">
        <button class="filter-btn active" data-filter="todos" onclick="filtrar('todos')">
            <i class="fas fa-list"></i> Todos
        </button>
        <button class="filter-btn" data-filter="aberto" onclick="filtrar('aberto')">
            <i class="fas fa-exclamation-circle"></i> Abertos
            <span class="badge-count">2</span>
        </button>
        <button class="filter-btn" data-filter="execucao" onclick="filtrar('execucao')">
            <i class="fas fa-cog fa-spin"></i> Em Execução
            <span class="badge-count">1</span>
        </button>
        <button class="filter-btn" data-filter="concluido" onclick="filtrar('concluido')">
            <i class="fas fa-check-circle"></i> Concluídos
            <span class="badge-count">2</span>
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Solicitante</th>
                <th>Assunto</th>
                <th>Prioridade</th>
                <th>Técnico</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabela-chamados">
            <?php
            // SIMULAÇÃO DO BANCO DE DADOS
            $chamados = [
                ['id'=>501, 'nome'=>'Carlos Silva', 'assunto'=>'Internet lenta', 'prio'=>'Alta', 'tec'=>'--', 'status'=>'aberto'],
                ['id'=>502, 'nome'=>'Ana Souza', 'assunto'=>'Instalar Office', 'prio'=>'Média', 'tec'=>'Roberto', 'status'=>'execucao'],
                ['id'=>503, 'nome'=>'Marcos Dias', 'assunto'=>'Mouse quebrado', 'prio'=>'Baixa', 'tec'=>'João', 'status'=>'concluido'],
                ['id'=>504, 'nome'=>'Julia Lima', 'assunto'=>'Erro no ERP', 'prio'=>'Crítica', 'tec'=>'--', 'status'=>'aberto'],
                ['id'=>505, 'nome'=>'Pedro Santos', 'assunto'=>'Troca de Toner', 'prio'=>'Baixa', 'tec'=>'João', 'status'=>'concluido'],
            ];

            foreach ($chamados as $c) {
                // Define a classe CSS baseada no status
                $cssClass = 'st-' . $c['status']; 
                // Formata o texto para exibição (ex: execucao -> Em Execução)
                $textoStatus = $c['status'] == 'execucao' ? 'Em Execução' : ucfirst($c['status']);
                
                // O atributo 'data-status' é o segredo para o filtro funcionar
                echo "<tr class='linha-chamado' data-status='{$c['status']}'>";
                echo "<td>#{$c['id']}</td>";
                echo "<td><strong>{$c['nome']}</strong></td>";
                echo "<td>{$c['assunto']}</td>";
                echo "<td>{$c['prio']}</td>";
                echo "<td>{$c['tec']}</td>";
                echo "<td><span class='status-badge {$cssClass}'>{$textoStatus}</span></td>";
                echo "<td>
                        <button class='btn-action' title='Editar'><i class='fas fa-edit'></i></button>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    function filtrar(status) {
        // 1. Atualizar visual dos botões
        const botoes = document.querySelectorAll('.filter-btn');
        botoes.forEach(btn => {
            btn.classList.remove('active');
            if(btn.getAttribute('data-filter') === status) {
                btn.classList.add('active');
            }
        });

        // 2. Filtrar as linhas da tabela
        const linhas = document.querySelectorAll('.linha-chamado');
        
        linhas.forEach(linha => {
            const statusLinha = linha.getAttribute('data-status');
            
            if (status === 'todos') {
                linha.style.display = 'table-row'; // Mostra tudo
            } else {
                if (statusLinha === status) {
                    linha.style.display = 'table-row'; // Mostra se bater com o filtro
                } else {
                    linha.style.display = 'none'; // Esconde
                }
            }
        });
    }
</script>

</body>
</html>