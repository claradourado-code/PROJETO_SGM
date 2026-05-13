<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Blocos</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { cursor: pointer; background-color: #28a745; color: white; border: none; }
        button.delete { background-color: #dc3545; width: auto; padding: 5px 10px; }
        button.edit { background-color: #ffc107; color: black; width: auto; padding: 5px 10px; }
        ul { list-style-type: none; padding: 0; }
        li { background: #f4f4f4; margin: 10px 0; padding: 10px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="container">
    <h2>Gerenciar Blocos</h2>
    
    <form id="blocoForm">
        <input type="hidden" id="blocoId">
        <input type="text" id="blocoNome" placeholder="Nome do Bloco" required>
        <button type="submit" id="btnSalvar">Salvar Bloco</button>
    </form>

    <hr>

    <h3>Lista de Blocos</h3>
    <ul id="listaBlocos"></ul>
</div>

<script>
    let blocos = [];
    let idAtual = 0;

    const form = document.getElementById('blocoForm');
    const lista = document.getElementById('listaBlocos');

    // Função para renderizar a lista (Read)
    function renderizarLista() {
        lista.innerHTML = '';
        blocos.forEach(bloco => {
            const li = document.createElement('li');
            li.innerHTML = `
                <span>${bloco.nome}</span>
                <div>
                    <button class="edit" onclick="editarBloco(${bloco.id})">Editar</button>
                    <button class="delete" onclick="deletarBloco(${bloco.id})">Excluir</button>
                </div>
            `;
            lista.appendChild(li);
        });
    }

    // Função para Salvar (Create / Update)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('blocoId').value;
        const nome = document.getElementById('blocoNome').value;

        if (id) {
            // Atualizar
            const index = blocos.findIndex(b => b.id == id);
            blocos[index].nome = nome;
        } else {
            // Criar
            idAtual++;
            blocos.push({ id: idAtual, nome: nome });
        }

        form.reset();
        document.getElementById('blocoId').value = '';
        renderizarLista();
    });

    // Função para Carregar dados no form (Preparar Update)
    window.editarBloco = function(id) {
        const bloco = blocos.find(b => b.id == id);
        document.getElementById('blocoId').value = bloco.id;
        document.getElementById('blocoNome').value = bloco.nome;
    };

    // Função para Deletar (Delete)
    window.deletarBloco = function(id) {
        if(confirm('Tem certeza que deseja excluir este bloco?')) {
            blocos = blocos.filter(b => b.id != id);
            renderizarLista();
        }
    };
</script>

</body>
</html>