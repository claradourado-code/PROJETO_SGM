<?php
session_start();
// Verifica se o usuário está logado e se é técnico
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') { 
    header("Location: login.php"); exit; 
}
$id = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGM - Atendimento Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .chat-box { height: 250px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .thumb-img { width: 80px; height: 80px; object-fit: cover; cursor: pointer; border-radius: 4px; border: 1px solid #ddd; transition: 0.2s; }
        .thumb-img:hover { opacity: 0.8; }
        .msg { background: white; padding: 8px; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="tecnico_minhas_tarefas.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <h4 class="mb-0">Ordem de Serviço #<?= $id ?></h4>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold">Detalhes do Chamado</div>
                    <div class="card-body">
                        <div id="detalhesTexto">Carregando informações...</div>
                        <div id="containerFotos" class="d-flex gap-2 flex-wrap mt-3"></div>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-dark text-white fw-bold">Diário de Bordo / Histórico</div>
                    <div class="card-body">
                        <div id="listaComentarios" class="chat-box mb-3"></div>
                        <div class="input-group">
                            <input type="text" id="txtMsg" class="form-control" placeholder="Digite uma atualização...">
                            <button class="btn btn-primary" onclick="enviarComentario()">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div id="cardAcao" class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white fw-bold">Ações do Atendimento</div>
                    <div class="card-body">
                        
                        <div id="areaIniciar" style="display:none;">
                            <p class="text-muted">O chamado está aguardando o início da execução.</p>
                            <button onclick="mudarStatus('iniciar')" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-play-fill"></i> INICIAR TRABALHO
                            </button>
                        </div>

                        <form id="formFinalizar" style="display:none;" enctype="multipart/form-data">
                            <input type="hidden" name="id_chamado" value="<?= $id ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Solução Aplicada</label>
                                <textarea name="solucao" class="form-control" rows="4" required placeholder="Relate o que foi consertado..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Evidência de Conclusão (Foto)</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-check-circle"></i> CONCLUIR SERVIÇO
                            </button>
                        </form>

                        <div id="areaConcluido" style="display:none;" class="text-center">
                            <div class="alert alert-info">
                                <i class="bi bi-lock-fill"></i> Esta Ordem de Serviço já foi concluída e aguarda encerramento do gestor.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-body p-0 text-center">
                    <img src="" id="imgModal" class="img-fluid">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Amplia a imagem no modal
        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        // Carrega dados do chamado e fotos
        async function carregarDados() {
            try {
                const res = await fetch(`api/chamados.php?id=<?= $id ?>`);
                const c = await res.json();

                document.getElementById('detalhesTexto').innerHTML = `
                    <p class="mb-1"><strong>Local:</strong> ${c.bloco_nome} - ${c.ambiente_nome}</p>
                    <p class="mb-1"><strong>Solicitante:</strong> ${c.solicitante_nome}</p>
                    <p class="mb-0"><strong>Descrição:</strong> ${c.descricao_problema}</p>
                `;

                // Controle de interface por status
                if (c.status === 'agendado') {
                    document.getElementById('areaIniciar').style.display = 'block';
                } else if (c.status === 'em_execucao') {
                    document.getElementById('formFinalizar').style.display = 'block';
                } else {
                    document.getElementById('areaConcluido').style.display = 'block';
                }

                // Carrega Anexos (Fotos de abertura/conclusão)
                const resAnexos = await fetch(`api/anexos.php?id_chamado=<?= $id ?>`);
                const anexos = await resAnexos.json();
                const container = document.getElementById('containerFotos');
                container.innerHTML = anexos.map(a => `
                    <div class="text-center">
                        <img src="${a.caminho_arquivo}" class="thumb-img" onclick="verFoto('${a.caminho_arquivo}')">
                        <small class="d-block text-muted" style="font-size:0.7rem">${a.tipo_anexo === 'abertura' ? 'Abertura' : 'Conclusão'}</small>
                    </div>
                `).join('');
                
                carregarComentarios();
            } catch (err) {
                console.error("Falha ao carregar dados:", err);
            }
        }

        // Iniciar Atendimento
        async function mudarStatus(acao) {
            const res = await fetch(`api/tecnico_acoes.php?acao=${acao}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id_chamado: <?= $id ?> })
            });
            const result = await res.json();
            if(result.success) location.reload();
        }

        // Finalizar com Upload e Redirecionar
        document.getElementById('formFinalizar').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            try {
                const res = await fetch('api/tecnico_acoes.php?acao=finalizar', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                
                if (result.success) {
                    alert("Serviço finalizado com sucesso!");
                    window.location.href = 'tecnico_minhas_tarefas.php';
                } else {
                    alert("Erro: " + result.message);
                }
            } catch (err) {
                alert("Erro na comunicação com o servidor.");
            }
        };

        // Carregar Comentários (Evita undefined usando fallback de chaves)
        async function carregarComentarios() {
            const res = await fetch(`api/comentarios.php?id_chamado=<?= $id ?>`);
            const dados = await res.json();
            
            const lista = document.getElementById('listaComentarios');
            if (dados.length === 0) {
                lista.innerHTML = '<small class="text-muted">Nenhuma atualização registrada.</small>';
                return;
            }

            lista.innerHTML = dados.map(m => `
                <div class="msg shadow-sm">
                    <small class="text-primary fw-bold">${m.nome}</small>
                    <div style="font-size: 0.9rem;">${m.texto || m.comentario || "..."}</div>
                </div>
            `).join('');
            
            lista.scrollTop = lista.scrollHeight; // Scroll automático para o fim
        }

        // Enviar novo comentário
        async function enviarComentario() {
            const input = document.getElementById('txtMsg');
            if(!input.value.trim()) return;

            await fetch('api/comentarios.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id_chamado: <?= $id ?>, comentario: input.value })
            });
            input.value = '';
            carregarComentarios();
        }

        // Inicializa a página
        carregarDados();
        // Atualiza o diário a cada 10 segundos
        setInterval(carregarComentarios, 10000);
    </script>
</body>
</html>