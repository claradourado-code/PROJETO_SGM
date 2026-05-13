<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuários</title>
    <style>
        /* Estilos básicos para deixar a tela bonita */
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2, h3 { color: #333; }
        
        /* Tabela */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        
        /* Botões */
        button { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-add { background-color: #28a745; color: white; margin-bottom: 15px; }
        .btn-add:hover { background-color: #218838; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; color: white; }
        
        /* Formulário */
        #form-section { display: none; background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold;}
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Gerenciamento de Usuários</h2>
        
        <button class="btn-add" onclick="abrirFormulario()">+ Novo Usuário</button>

        <div id="form-section">
            <h3>Cadastrar / Editar Usuário</h3>
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" id="nome" placeholder="Ex: João da Silva">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="email" placeholder="Ex: joao@email.com">
            </div>
            <button class="btn-add" onclick="salvarUsuario()">Salvar</button>
            <button onclick="fecharFormulario()" style="background: #6c757d; color: white;">Cancelar</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="lista-usuarios">
                <tr>
                    <td>1</td>
                    <td>Maria Souza</td>
                    <td>maria.souza@email.com</td>
                    <td>
                        <button class="btn-edit" onclick="editarUsuario()">Editar</button>
                        <button class="btn-delete" onclick="deletarUsuario()">Excluir</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        function abrirFormulario() {
            document.getElementById('form-section').style.display = 'block';
            document.getElementById('nome').value = ''; // Limpa o campo
            document.getElementById('email').value = ''; // Limpa o campo
        }

        function fecharFormulario() {
            document.getElementById('form-section').style.display = 'none';
        }

        function salvarUsuario() {
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            
            if(nome === '' || email === '') {
                alert("Por favor, preencha todos os campos!");
                return;
            }

            alert("Você clicou em salvar! O usuário " + nome + " seria salvo no banco de dados agora.");
            fecharFormulario();
        }

        function editarUsuario() {
            abrirFormulario();
            // Aqui você puxaria os dados da tabela para os campos do form
            alert("Aqui você preencheria os inputs com os dados da pessoa para alterar.");
        }

        function deletarUsuario() {
            const confirmacao = confirm("Tem certeza que deseja excluir este usuário?");
            if (confirmacao) {
                alert("Usuário excluído!");
            }
        }
    </script>
</body>
</html>