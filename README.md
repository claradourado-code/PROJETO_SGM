# SGM - Sistema de Gestão de Manutenção

O **SGM** é uma solução completa para gestão de ordens de serviço e manutenção predial, projetada para simplificar a comunicação entre solicitantes, técnicos e gestores. A aplicação utiliza uma estética moderna (*Glassmorphism*) e um fluxo de trabalho otimizado.

## 🚀 Funcionalidades Principais

- **Perfil Gestor:** Dashboard com indicadores em tempo real, triagem de chamados, atribuição de técnicos, gestão de locais (Blocos/Ambientes) e tipos de serviço.
- **Perfil Técnico:** Agenda de tarefas organizada por prioridade, fluxo de atendimento com cronômetro e relatório de conclusão com fotos.
- **Perfil Solicitante:** Abertura rápida de chamados com anexos fotográficos e acompanhamento de status.
- **Design Premium:** Interface responsiva, moderna e intuitiva baseada em Bootstrap 5 e CSS personalizado.

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.x (Nativo)
- **Banco de Dados:** MySQL / MariaDB
- **Frontend:** HTML5, JS Vanilla (ES6+), Bootstrap 5, Bootstrap Icons
- **Estilização:** CSS Customizado com foco em Rich Aesthetics (Glassmorphism)

## 📦 Instalação e Configuração

1. **Servidor:** Utilize um ambiente como XAMPP, WAMP ou Laragon.
2. **Diretório:** Clone o projeto para dentro da pasta `htdocs` (ou equivalente).
3. **Banco de Dados:**
   - Crie um banco de dados chamado `sgm_db`.
   - Importe o arquivo SQL disponível abaixo (ou em `docs/BancoDeDados.sql`).
4. **Configuração:** Ajuste as credenciais de acesso ao banco em `config/database.php`.

## 🔑 Credenciais de Teste

Todos os usuários abaixo utilizam a senha padrão: **`123456`**

| Perfil | E-mail | Descrição |
| :--- | :--- | :--- |
| **Gestor** | `admin@sgm.com` | Acesso total ao sistema e gestão. |
| **Técnico** | `tecnico@sgm.com` | Visualização e execução de tarefas atribuídas. |
| **Solicitante** | `usuario@sgm.com` | Abertura e consulta de solicitações. |

---

## 🗄️ SQL para Seed do Banco de Dados (Testes)

Copie e cole o código abaixo no seu gerenciador de banco de dados (ex: phpMyAdmin) para criar a estrutura e popular os dados iniciais:

```sql
-- SCRIPT DE CRIAÇÃO E POPULAÇÃO SGM
CREATE DATABASE IF NOT EXISTS sgm_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgm_db;

-- 1. USUÁRIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('solicitante', 'tecnico', 'gestor') NOT NULL DEFAULT 'solicitante',
    ativo TINYINT(1) DEFAULT 1,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE INDEX idx_email (email)
) ENGINE=InnoDB;

-- 2. BLOCOS E AMBIENTES
CREATE TABLE IF NOT EXISTS blocos (
    id_bloco INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_bloco)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ambientes (
    id_ambiente INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    id_bloco INT NOT NULL,
    PRIMARY KEY (id_ambiente),
    FOREIGN KEY (id_bloco) REFERENCES blocos (id_bloco) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. SERVIÇOS E CHAMADOS
CREATE TABLE IF NOT EXISTS tipos_servico (
    id_tipo INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(200),
    PRIMARY KEY (id_tipo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS chamados (
    id_chamado INT NOT NULL AUTO_INCREMENT,
    descricao_problema TEXT NOT NULL,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('aberto', 'agendado', 'em_execucao', 'concluido', 'fechado', 'cancelado') DEFAULT 'aberto',
    prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'baixa',
    data_previsao_conclusao DATE DEFAULT NULL,
    solucao_tecnica TEXT,
    tempo_gasto_minutos INT DEFAULT NULL,
    id_solicitante INT NOT NULL,
    id_tecnico INT DEFAULT NULL,
    id_ambiente INT NOT NULL,
    id_tipo_servico INT NOT NULL,
    PRIMARY KEY (id_chamado),
    FOREIGN KEY (id_solicitante) REFERENCES usuarios (id_usuario),
    FOREIGN KEY (id_tecnico) REFERENCES usuarios (id_usuario),
    FOREIGN KEY (id_ambiente) REFERENCES ambientes (id_ambiente),
    FOREIGN KEY (id_tipo_servico) REFERENCES tipos_servico (id_tipo)
) ENGINE=InnoDB;

-- 4. ANEXOS
CREATE TABLE IF NOT EXISTS chamados_anexos (
    id_anexo INT NOT NULL AUTO_INCREMENT,
    caminho_arquivo VARCHAR(255) NOT NULL,
    tipo_anexo ENUM('abertura', 'conclusao') NOT NULL,
    id_chamado INT NOT NULL,
    PRIMARY KEY (id_anexo),
    FOREIGN KEY (id_chamado) REFERENCES chamados (id_chamado) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SEED DE DADOS (Senha '123456' hash real)
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES 
('Admin Gestor', 'admin@sgm.com', '$2y$10$gBWSPw8HnKtioh4flBc6WuCpU4Va2Dni2.McCi5Bc1yFa.L0u26r.', 'gestor'),
('João Técnico', 'tecnico@sgm.com', '$2y$10$gBWSPw8HnKtioh4flBc6WuCpU4Va2Dni2.McCi5Bc1yFa.L0u26r.', 'tecnico'),
('Maria Solicitante', 'usuario@sgm.com', '$2y$10$gBWSPw8HnKtioh4flBc6WuCpU4Va2Dni2.McCi5Bc1yFa.L0u26r.', 'solicitante');

INSERT INTO tipos_servico (nome, descricao) VALUES 
('Elétrica', 'Reparos em rede elétrica e iluminação'),
('Hidráulica', 'Vazamentos e tubulações'),
('Civil/Predial', 'Pintura, alvenaria e reparos estruturais');

INSERT INTO blocos (nome) VALUES ('Bloco Administrativo'), ('Produção');
INSERT INTO ambientes (nome, id_bloco) VALUES ('Recepção', 1), ('Copa', 1), ('Linha de Produção 01', 2);
```
