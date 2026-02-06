<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Solicitante</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; padding: 20px; }
        
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f1f1f1; }

        /* Estilos para as badges de status */
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 0.85em; color: white; font-weight: bold; }
        .status-pendente { background-color: #ffc107; color: #333; }
        .status-andamento { background-color: #17a2b8; }
        .status-concluido { background-color: #28a745; }

        .btn-ver { background: #6c757d; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
        <h2><i class="fas fa-list-alt"></i> Minhas Solicitações</h2>
        <button type="button" class="btn btn-success">Success</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Assunto</th>
                    <th>Departamento</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                
                </tr>
            </thead>
            <tbody>
                <?php
                // SIMULAÇÃO DE DADOS (Substituir por conexão MySQL depois)
                $solicitacoes = [
                    ['id' => 101, 'assunto' => 'Troca de Monitor', 'depto' => 'TI', 'data' => '05/02/2026', 'status' => 'aberto'],
                    ['id' => 102, 'assunto' => 'Reembolso Viagem', 'depto' => 'Financeiro', 'data' => '01/02/2026', 'status' => 'pendente'],
                    ['id' => 103, 'assunto' => 'Acesso ao VPN', 'depto' => 'TI', 'data' => '20/01/2026', 'status' => 'concluido'],
                    ['id' => 104, 'assunto' => 'Compra de Material', 'depto' => 'Almoxarifado', 'data' => '15/01/2026', 'status' => 'concluido'],
                ];

                if (count($solicitacoes) > 0) {
                    foreach ($solicitacoes as $sol) {
                        // Lógica simples para cores dos status
                        $classeStatus = $sol['status']; // aberto, pendente, concluido
                        $textoStatus = ucfirst($sol['status']);

                        echo "<tr>";
                        echo "<td>#{$sol['id']}</td>";
                        echo "<td>{$sol['assunto']}</td>";
                        echo "<td>{$sol['depto']}</td>";
                        echo "<td>{$sol['data']}</td>";
                        echo "<td><span class='status {$classeStatus}'>{$textoStatus}</span></td>";
                        echo "<td><button onclick='verDetalhes({$sol['id']})' style='cursor:pointer; border:none; background:transparent; color:#007bff;'><i class='fas fa-eye'></i></button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nenhuma solicitação encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <button class="btn-flutuante" onclick="abrirNovaSolicitacao()" title="Criar Nova Solicitação">
        <i class="fas fa-plus"></i>
    </button>

    <script>
        // Função JavaScript para o Botão Flutuante
        function abrirNovaSolicitacao() {
            // Aqui você pode redirecionar para uma página ou abrir um Modal
            // Exemplo de redirecionamento: window.location.href = 'nova_solicitacao.php';
            
            alert("🚀 Ação: Abrir formulário de Nova Solicitação!");
            console.log("Usuário clicou no botão pulsante.");
        }

        function verDetalhes(id) {
            alert("Ver detalhes da solicitação ID: " + id);
        }
    </script>

</body>
</html>