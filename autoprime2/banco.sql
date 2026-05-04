-- ============================================================
--  AutoPrime — Script completo de banco de dados
--  Execute no HeidiSQL (Laragon) ou no terminal MySQL
-- ============================================================

-- 1. Cria e seleciona o banco
CREATE DATABASE IF NOT EXISTS autoprime
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE autoprime;

-- ============================================================
-- 2. TABELA: usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  nome     VARCHAR(120)  NOT NULL,
  email    VARCHAR(180)  NOT NULL UNIQUE,
  senha    VARCHAR(255)  NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuário padrão: admin@autoprime.com / senha: admin123
INSERT INTO usuarios (nome, email, senha) VALUES
  ('Administrador', 'admin@autoprime.com', MD5('admin123'));

-- ============================================================
-- 3. TABELA: clientes
-- ============================================================
CREATE TABLE IF NOT EXISTS clientes (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nome      VARCHAR(150) NOT NULL,
  telefone  VARCHAR(25)  DEFAULT NULL,
  email     VARCHAR(180) DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO clientes (nome, telefone, email) VALUES
  ('Daniel Henrique Aquino de Souza Celeste', '(31) 67676-7767', 'danielceleste@gmail.com'),
  ('Rafael Aura',                             '(31) 43652-04',   'rafaelaura@gmail.com'),
  ('Caio Beta',                               '(31) 76337-83',   'caiobeta@gmail.com'),
  ('Bolsonaro',                               '(31) 97437-34',   'bolsonaro@gmail.com'),
  ('Luiz Inácio da Silva',                    '(31) 98423-23',   'luizinacio@gmail.com');

-- ============================================================
-- 4. TABELA: veiculos
-- ============================================================
CREATE TABLE IF NOT EXISTS veiculos (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  modelo         VARCHAR(100) NOT NULL,
  ano            YEAR         NOT NULL,
  cor            VARCHAR(60)  NOT NULL,
  cor_hex        VARCHAR(7)   DEFAULT '#cccccc',
  placa          VARCHAR(10)  DEFAULT NULL,
  diaria         DECIMAL(10,2) DEFAULT 120.00,
  status         ENUM('disponivel','alugado','manutencao') NOT NULL DEFAULT 'disponivel',
  cliente_id     INT          DEFAULT NULL,
  data_inicio    DATE         DEFAULT NULL,
  data_devolucao DATE         DEFAULT NULL,
  criado_em      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_veiculo_cliente
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO veiculos (modelo, ano, cor, cor_hex, placa, diaria, status, cliente_id, data_inicio, data_devolucao) VALUES
  ('HB20',    2017, 'Preto',  '#1a1a1a', 'ABC-1234', 120.00, 'disponivel', NULL, NULL, NULL),
  ('HB20',    2017, 'Preto',  '#1a1a1a', 'DEF-5678', 120.00, 'alugado',    2,    DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY)),
  ('Fusca',   1959, 'Azul',   '#1d4ed8', 'GHI-9012', 90.00,  'alugado',    3,    DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
  ('Vectra',  2017, 'Prata',  '#9ca3af', 'JKL-3456', 150.00, 'alugado',    4,    DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
  ('Camaro',  2022, 'Amarelo','#fbbf24', 'MNO-7890', 350.00, 'disponivel', NULL, NULL, NULL),
  ('Corolla', 2023, 'Branco', '#f9fafb', 'PQR-1122', 180.00, 'disponivel', NULL, NULL, NULL),
  ('Civic',   2021, 'Cinza',  '#6b7280', 'STU-3344', 160.00, 'manutencao', NULL, NULL, NULL);

-- ============================================================
-- 5. TABELA: locacoes (histórico)
-- ============================================================
CREATE TABLE IF NOT EXISTS locacoes (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  veiculo_id          INT  NOT NULL,
  cliente_id          INT  NOT NULL,
  data_inicio         DATE NOT NULL,
  data_devolucao      DATE NOT NULL,
  data_devolucao_real DATE DEFAULT NULL,
  status              ENUM('ativo','finalizado','cancelado') NOT NULL DEFAULT 'ativo',
  criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_loc_veiculo FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE,
  CONSTRAINT fk_loc_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Locações ativas (espelham os veículos alugados acima)
INSERT INTO locacoes (veiculo_id, cliente_id, data_inicio, data_devolucao, status) VALUES
  (2, 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'ativo'),
  (3, 3, DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'ativo'),
  (4, 4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'ativo');

-- ============================================================
-- Resumo do banco criado:
--
--  usuarios  → 1 registro  (login: admin@autoprime.com / admin123)
--  clientes  → 5 registros
--  veiculos  → 7 registros (3 disponíveis, 3 alugados, 1 manutenção)
--  locacoes  → 3 registros ativos
-- ============================================================
