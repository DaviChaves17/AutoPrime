<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['logado'])) { header('Location: home.php'); exit; }

require_once __DIR__ . '/includes/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (!$email || !$senha) {
        $erro = 'Preencha o e-mail e a senha.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        $ok = $u && (md5($senha) === $u['senha'] || password_verify($senha, $u['senha']));

        if ($ok) {
            $_SESSION['logado']       = true;
            $_SESSION['usuario_id']   = $u['id'];
            $_SESSION['usuario_nome'] = $u['nome'];
            header('Location: home.php');
            exit;
        } else {
            $erro = 'E-mail ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoPrime — Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/css/autoprime.css" rel="stylesheet">
</head>
<body>

<div class="ap-login-bg">
  <div class="ap-login-card">

    <div class="text-center">
      <div class="ap-login-logo">
        <img src="assets/img/logo.png" alt="AutoPrime">
      </div>
    </div>

    <?php if ($erro): ?>
    <div class="ap-alert ap-alert-danger">
      <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
      <?= htmlspecialchars($erro) ?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>

      <div class="mb-3">
        <label class="ap-label" for="email">E-mail</label>
        <input id="email" name="email" type="text" class="ap-input"
          placeholder="admin@autoprime.com"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          autocomplete="username">
      </div>

      <div class="mb-4">
        <label class="ap-label" for="senha">Senha</label>
        <div class="ap-input-group">
          <input id="senha" name="senha" type="password" class="ap-input"
            placeholder="••••••••" autocomplete="current-password">
          <button type="button" class="ap-eye" onclick="toggleSenha()">
            <i class="bi bi-eye" id="eye-icon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="ap-btn ap-btn-red ap-btn-full" style="padding:.9rem;">
        Entrar <i class="bi bi-arrow-right ms-1"></i>
      </button>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSenha() {
  const c = document.getElementById('senha');
  const i = document.getElementById('eye-icon');
  c.type = c.type === 'password' ? 'text' : 'password';
  i.className = c.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
</body>
</html>
