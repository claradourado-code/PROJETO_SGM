<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados - Área do Solicitante</title>
    
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        
        /* Estilo da Tabela */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f1f1f1; }

        /* Cores para o Status */
        .status-aberto { color: green; font-weight: bold; }
        .status-fechado { color: red; font-weight: bold; }
        .status-pendente { color: orange; font-weight: bold; }

        /* Mensagem de carregando/erro */
        #mensagem { text-align: center; color: #666; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Meus Chamados</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Local</th>
                <th>Descrição</th>
                <th>Data Abertura</th>
                <th>Status</th>
                <th>Ações</th> </tr>
        </thead>
        <tbody id="tabela-corpo">
            </tbody>
    </table>
    <p id="mensagem">Carregando chamados...</p>
</div>

<script>
    // URL da sua API (ajuste se estiver em outra pasta)
    const API_URL = 'chamados.php';

    async function carregarChamados() {
        try {
            const response = await fetch(API_URL);
            const dados = await response.json();

            const corpoTabela = document.getElementById('tabela-corpo');
            const mensagem = document.getElementById('mensagem');

            // Limpa a tabela antes de preencher
            corpoTabela.innerHTML = '';

            if (dados.sucesso && dados.dados.length > 0) {
                mensagem.style.display = 'none'; // Esconde mensagem de carregando

                // Loop para criar cada linha da tabela
                dados.dados.forEach(chamado => {
                    const tr = document.createElement('tr');

                    // Formatar data para PT-BR
                    const dataFormatada = new Date(chamado.data).toLocaleDateString('pt-BR', {
                        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'
                    });

                    // Define classe de cor baseada no status
                    let classeStatus = 'status-aberto';
                    if(chamado.status.toLowerCase() === 'fechado') classeStatus = 'status-fechado';
                    if(chamado.status.toLowerCase() === 'pendente') classeStatus = 'status-pendente';

                    tr.innerHTML = `
                        <td>#${chamado.id}</td>
                        <td>${chamado.local}</td>
                        <td>${chamado.descricao}</td>
                        <td>${dataFormatada}</td>
                        <td class="${classeStatus}">${chamado.status}</td>
                        <td>
                            <button onclick="verAnexos(${chamado.id})">Ver Anexos</button>
                        </td>
                    `;
                    corpoTabela.appendChild(tr);
                });
            } else {
                mensagem.textContent = 'Nenhum chamado encontrado.';
            }

        } catch (error) {
            console.error('Erro:', error);
            document.getElementById('mensagem').textContent = 'Erro ao carregar chamados. Verifique se você está logado.';
        }
    }

    // Função para o botão de ver anexos (Simulação)
    function verAnexos(id) {
        alert('Aqui você abriria os anexos do ID: ' + id);
        // Exemplo: window.location.href = 'visualizar_chamado.php?id=' + id;
    }

    // Carrega a tabela assim que a página abre
    carregarChamados();
</script>

</body>
</html>