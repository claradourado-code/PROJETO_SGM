<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    header("Location: login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Nova Solicitação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="solicitante_dashboard.php">SGM</a>
            <a href="solicitante_dashboard.php" class="btn btn-sm btn-outline-light"><i class="bi bi-chevron-left me-1"></i> Voltar</a>
        </div>
    </nav>

    <div class="container animate-fade-in pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="glass-card p-4 p-md-5">
                    <h3 class="fw-bold mb-4 text-center">Abrir Chamado</h3>
                    
                    <form id="formChamado">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Localização (Bloco)</label>
                            <select id="selectBloco" class="form-select border-0 shadow-sm" required onchange="carregarAmbientes(this.value)">
                                <option value="">Selecione o Bloco...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Ambiente / Sala</label>
                            <select id="selectAmbiente" class="form-select border-0 shadow-sm" required disabled>
                                <option value="">Selecione o Bloco primeiro...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tipo de Serviço</label>
                            <select id="selectTipo" class="form-select border-0 shadow-sm" required>
                                <option value="">Selecione o tipo...</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Descrição do Problema</label>
                            <textarea id="descricao" class="form-control border-0 shadow-sm" rows="5" required placeholder="Descreva detalhadamente o que está acontecendo..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Foto da Ocorrência (Opcional)</label>
                            <div class="input-group">
                                <input type="file" id="foto" class="form-control border-0 shadow-sm" accept="image/*">
                                <span class="input-group-text bg-white border-0"><i class="bi bi-camera"></i></span>
                            </div>
                        </div>
                        
                        <button type="submit" id="btnSubmit" class="btn btn-primary w-100 py-3 shadow">
                            Registrar Solicitação <i class="bi bi-send-fill ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function iniciar() {
            try {
                const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
                const blocos = await resB.json();
                const selB = document.getElementById('selectBloco');
                blocos.forEach(b => selB.innerHTML += `<option value="${b.id_bloco}">${b.nome}</option>`);

                const resT = await fetch('api/localizacoes.php?acao=listar_tipos');
                const tipos = await resT.json();
                const selT = document.getElementById('selectTipo');
                tipos.forEach(t => selT.innerHTML += `<option value="${t.id_tipo}">${t.nome}</option>`);
            } catch(e) { console.error("Erro ao iniciar formulário:", e); }
        }

        async function carregarAmbientes(id_bloco) {
            const selA = document.getElementById('selectAmbiente');
            if (!id_bloco) { selA.disabled = true; selA.innerHTML = '<option value="">Selecione o Bloco primeiro...</option>'; return; }
            
            selA.disabled = false;
            selA.innerHTML = '<option value="">Carregando salas...</option>';
            
            try {
                const res = await fetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`);
                const ambientes = await res.json();
                
                selA.innerHTML = '<option value="">Selecione a Sala...</option>';
                ambientes.forEach(a => selA.innerHTML += `<option value="${a.id_ambiente}">${a.nome}</option>`);
            } catch(e) { selA.innerHTML = '<option value="">Erro ao carregar</option>'; }
        }

        document.getElementById('formChamado').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';

            const formData = new FormData();
            formData.append('id_ambiente', document.getElementById('selectAmbiente').value);
            formData.append('id_tipo', document.getElementById('selectTipo').value);
            formData.append('descricao', document.getElementById('descricao').value);
            const fotoFile = document.getElementById('foto').files[0];
            if (fotoFile) formData.append('foto', fotoFile);

            try {
                const response = await fetch('api/salvar_chamado.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    window.location.href = 'solicitante_dashboard.php';
                } else {
                    alert("Erro: " + result.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Registrar Solicitação <i class="bi bi-send-fill ms-2"></i>';
                }
            } catch(e) { 
                alert("Erro ao conectar com o servidor."); 
                btn.disabled = false; 
                btn.innerHTML = 'Registrar Solicitação <i class="bi bi-send-fill ms-2"></i>';
            }
        });

        iniciar();
    </script>
</body>
</html>