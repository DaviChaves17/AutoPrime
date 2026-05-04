<?php
$tituloPagina = 'Editar Cliente';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Cliente não encontrado.'];
    header('Location: clientes.php'); exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if (!$nome) {
        $erro = 'O nome é obrigatório.';
    } else {
        $pdo->prepare("UPDATE clientes SET nome=?, telefone=?, email=? WHERE id=?")
            ->execute([$nome, $telefone, $email, $id]);
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => "Cliente {$nome} atualizado!"];
        header('Location: clientes.php'); exit;
    }
    $c = array_merge($c, $_POST);
}
?>

<div class="ap-page" style="padding-bottom:.5rem;">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-pencil-fill me-2" style="color:var(--ap-red);"></i>Editar Cliente
    </h1>
    <a href="clientes.php" class="ap-btn ap-btn-outline ap-btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
  </div>
</div>

<div class="ap-modal-page">
  <div class="ap-form-card">

    <div class="ap-form-card-header">
      <div class="ap-form-card-title">
        <i class="bi bi-person-fill" style="color:var(--ap-red);"></i>
        <?= htmlspecialchars($c['nome']) ?>
      </div>
      <span class="ap-id-badge">#<?= $c['id'] ?></span>
    </div>

    <div class="ap-form-card-body">
      <?php if ($erro): ?>
      <div class="ap-alert ap-alert-danger">
        <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <?= htmlspecialchars($erro) ?>
      </div>
      <?php endif; ?>

      <form method="POST" novalidate id="form-editar">
        <div class="mb-3">
          <label class="ap-label" for="nome">Nome Completo *</label>
          <input type="text" name="nome" id="nome" class="ap-input"
            value="<?= htmlspecialchars($c['nome']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="ap-label" for="telefone">Telefone</label>
          <input type="text" name="telefone" id="telefone" class="ap-input"
            value="<?= htmlspecialchars($c['telefone'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="ap-label" for="email">E-mail</label>
          <input type="email" name="email" id="email" class="ap-input"
            value="<?= htmlspecialchars($c['email'] ?? '') ?>">
        </div>
      </form>
    </div>

    <div class="ap-form-card-footer">
      <a href="clientes.php" class="ap-btn ap-btn-outline">Cancelar</a>
      <button type="submit" form="form-editar" class="ap-btn ap-btn-red">
        <i class="bi bi-check-circle-fill me-1"></i>Salvar Alterações
      </button>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
