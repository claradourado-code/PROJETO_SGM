<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Serviços</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; color: #333; }
        h1, h2 { color: #2c3e50; }
        .form-container { background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        
        /* Botões */
        button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-salvar { background-color: #27ae60; }
        .btn-salvar:hover { background-color: #219150; }
        .btn-editar { background-color: #f39c12; margin-right: 5px; }
        .btn-excluir { background-color: #c0392b; }
        
        /* Tabela */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
    </style>
</head>
<body>

    <h1>Gerenciar Serviços</h1>

    <div class="form-container">
        <h2 id="tituloFormulario">Adicionar Novo Serviço</h2>
        <form id="formServico">
            <input type="hidden" id="servicoId">
            
            <div class="form-group">
                <label for="nomeServico">Nome do Serviço:</label>
                <input type="text" id="nomeServico" placeholder="Ex: Manutenção de Computador" required>
            </div>
            
            <div class="form-group">
                <label for="descServico">Descrição do Serviço:</label>
                <textarea id="descServico" placeholder="Descreva os detalhes e o que está incluso no serviço..." required></textarea>
            </div>
            
            <button type="submit" class="btn-salvar">Salvar Serviço</button>
        </form>
    </div>

    <h2>Serviços Cadastrados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th style="width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaServicos">
            </tbody>
    </table>

    <script>
        // Banco de dados simulado (Array na memória)
        let servicos = [];
        let idAtual = 1;

        const form = document.getElementById('formServico');
        const tabela = document.getElementById('tabelaServicos');
        const tituloFormulario = document.getElementById('tituloFormulario');

        // Função para Renderizar (Lido do "Banco de Dados")
        function atualizarTabela() {
            tabela.innerHTML = '';
            
            if (servicos.length === 0) {
                tabela.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nenhum serviço cadastrado ainda.</td></tr>';
                return;
            }

            servicos.forEach(servico => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${servico.id}</td>
                    <td><strong>${servico.nome}</strong></td>
                    <td>${servico.descricao}</td>
                    <td>
                        <button class="btn-editar" onclick="editarServico(${servico.id})">Editar</button>
                        <button class="btn-excluir" onclick="deletarServico(${servico.id})">Excluir</button>
                    </td>
                `;
                tabela.appendChild(tr);
            });
        }

        // Função para Criar e Atualizar
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita que a página recarregue
            
            const id = document.getElementById('servicoId').value;
            const nome = document.getElementById('nomeServico').value;
            const descricao = document.getElementById('descServico').value;

            if (id) {
                // Atualizar existente (UPDATE)
                const index = servicos.findIndex(s => s.id == id);
                servicos[index] = { id: parseInt(id), nome, descricao };
                tituloFormulario.innerText = "Adicionar Novo Serviço";
            } else {
                // Criar novo (CREATE)
                servicos.push({ id: idAtual++, nome, descricao });
            }

            // Limpa o formulário e atualiza a tabela
            form.reset();
            document.getElementById('servicoId').value = '';
            atualizarTabela();
        });

        // Função para Carregar dados no formulário (Preparar o UPDATE)
        function editarServico(id) {
            const servico = servicos.find(s => s.id === id);
            document.getElementById('servicoId').value = servico.id;
            document.getElementById('nomeServico').value = servico.nome;
            document.getElementById('descServico').value = servico.descricao;
            
            tituloFormulario.innerText = "Editando Serviço: #" + servico.id;
            window.scrollTo(0, 0); // Sobe a página para o formulário
        }

        // Função para Deletar (DELETE)
        function deletarServico(id) {
            if (confirm("Tem certeza que deseja excluir este serviço?")) {
                servicos = servicos.filter(s => s.id !== id);
                atualizarTabela();
            }
        }

        // Renderiza a tabela inicial vazia
        atualizarTabela();
    </script>
</body>
</html>