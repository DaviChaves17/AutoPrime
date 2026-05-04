# 🚗 AutoPrime — Locadora de Veículos

Sistema web de gerenciamento de locadora de veículos desenvolvido em **PHP + MySQL + Bootstrap 5**.

---

## 📋 Requisitos

- [Laragon](https://laragon.org/download/) (versão Full ou Lite)
- PHP 8.0 ou superior (já incluso no Laragon)
- MySQL 8.0 (já incluso no Laragon)
- Navegador moderno (Chrome, Firefox, Edge)

---

## 🚀 Como executar

### 1. Instale o Laragon

Baixe e instale o Laragon em [laragon.org](https://laragon.org/download/).  
Após instalar, abra o Laragon e clique em **Start All** para iniciar Apache e MySQL.

---

### 2. Copie o projeto

Extraia o conteúdo do ZIP dentro da pasta raiz do Laragon:

```
C:\laragon\www\autoprime2\
```

A estrutura deve ficar assim:

```
C:\laragon\www\autoprime2\
├── index.php
├── home.php
├── veiculos.php
├── clientes.php
├── alugar.php
├── desalugar.php
├── exibir_veiculo.php
├── editar_veiculo.php
├── novo_veiculo.php
├── novo_cliente.php
├── editar_cliente.php
├── logout.php
├── banco.sql
├── assets/
│   ├── css/autoprime.css
│   └── img/logo.png, logo_nav.png
└── includes/
    ├── auth.php
    ├── conexao.php
    ├── header.php
    └── footer.php
```

---

### 3. Crie o banco de dados

Abra o **HeidiSQL** pelo Laragon:

> Laragon → clique em **DB** (ou botão direito no ícone da bandeja → HeidiSQL)

Dentro do HeidiSQL:

1. Conecte na sessão padrão (`root` sem senha, porta `3306`)
2. No menu superior: **Arquivo → Executar arquivo SQL...**
3. Selecione o arquivo `banco.sql` dentro da pasta do projeto
4. Clique em **Executar**

O banco `autoprime` será criado automaticamente com todas as tabelas e dados de exemplo.

---

### 4. Acesse o sistema

Abra o navegador e acesse:

```
http://localhost/autoprime2
```

ou, se o Laragon estiver configurado com Pretty URLs:

```
http://autoprime2.test
```

---

### 5. Login padrão

| Campo | Valor |
|-------|-------|
| **E-mail** | `admin@autoprime.com` |
| **Senha** | `admin123` |

---

## 📦 Dados de exemplo incluídos no banco

O arquivo `banco.sql` já popula o banco com:

| Tabela | Registros |
|--------|-----------|
| Usuários | 1 (admin) |
| Clientes | 5 |
| Veículos | 7 (3 disponíveis, 3 alugados, 1 manutenção) |
| Locações | 3 ativas |

---

## 🗂️ Funcionalidades

### Veículos
- ✅ Listar todos os veículos com status colorido
- ✅ Cadastrar novo veículo (modelo, ano, cor, placa, diária)
- ✅ Editar veículo
- ✅ Excluir veículo (bloqueado se estiver alugado)
- ✅ Exibir detalhes completos
- ✅ Enviar para manutenção / Marcar como disponível
- ✅ Alugar veículo disponível
- ✅ Registrar devolução (desalugar)

### Clientes
- ✅ Listar clientes com carro atual
- ✅ Cadastrar novo cliente
- ✅ Editar cliente
- ✅ Excluir cliente (bloqueado se tiver locação ativa)

### Dashboard (Home)
- ✅ Contadores em tempo real: disponíveis, alugados, em manutenção

---

## ⚙️ Configuração do banco (se necessário)

Caso precise alterar as credenciais do banco, edite o arquivo:

```
includes/conexao.php
```

```php
$host    = 'localhost';
$porta   = '3306';       // porta padrão do Laragon
$banco   = 'autoprime';
$usuario = 'root';
$senha   = '';           // Laragon não usa senha por padrão
```

---

## 🛠️ Tecnologias utilizadas

| Tecnologia | Versão |
|-----------|--------|
| PHP | 8.0+ |
| MySQL | 8.0 |
| Bootstrap | 5.3.3 |
| Bootstrap Icons | 1.11.3 |
| Google Fonts (Outfit + DM Sans) | — |

---

## 📁 Estrutura de arquivos

```
includes/
├── auth.php      → Funções de sessão: protegerPagina() e ativo()
├── conexao.php   → Conexão PDO com o MySQL
├── header.php    → HTML inicial + navbar (incluído em todas as páginas)
└── footer.php    → Scripts JS + fechamento do HTML

assets/
├── css/autoprime.css   → Estilos customizados do sistema
└── img/
    ├── logo.png        → Logo grande (tela de login)
    └── logo_nav.png    → Logo pequena (navbar)
```

---

## 🔐 Como funciona a autenticação

Cada página protegida inclui `header.php`, que já chama `auth.php`.  
A função `protegerPagina()` verifica se `$_SESSION['logado']` existe — caso contrário, redireciona para o login.

```php
<?php
$tituloPagina = 'Nome da Página';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();
// ... código da página
require_once __DIR__ . '/includes/footer.php';
?>
```

---

Desenvolvido para fins acadêmicos — AutoPrime Locadora de Veículos.
