<?php
$tituloPagina = 'Clientes';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

if (isset($_GET['deletar'])) {
    $did = (int)$_GET['deletar'];
    $chk = $pdo->prepare("SELECT COUNT(*) FROM veiculos WHERE cliente_id=? AND status='alugado'");
    $chk->execute([$did]);
    if ($chk->fetchColumn() > 0) {
        $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Não é possível excluir: cliente possui locação ativa.'];
    } else {
        $pdo->prepare("DELETE FROM clientes WHERE id=?")->execute([$did]);
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => 'Cliente excluído com sucesso.'];
    }
    header('Location: clientes.php');
    exit;
}

$clientes = $pdo->query("
    SELECT c.*, v.modelo AS carro_atual
    FROM clientes c
    LEFT JOIN veiculos v ON v.cliente_id = c.id AND v.status = 'alugado'
    ORDER BY c.id ASC
")->fetchAll();
?>

<div class="ap-page">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-people-fill me-2" style="color:var(--ap-red);"></i>Clientes
    </h1>
    <a href="novo_cliente.php" class="ap-btn ap-btn-red ap-btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Novo Cliente
    </a>
  </div>

  <div class="ap-table-wrap">
    <table class="ap-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Telefone</th>
          <th>E-mail</th>
          <th>Carro Atual</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($clientes)): ?>
          <tr><td colspan="6"><div class="ap-empty">Nenhum cliente cadastrado.</div></td></tr>
        <?php else: ?>
          <?php foreach ($clientes as $c): ?>
          <tr>
            <td><span class="ap-id-badge">#<?= $c['id'] ?></span></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="ap-avatar"><?= strtoupper(mb_substr($c['nome'], 0, 2)) ?></div>
                <strong><?= htmlspecialchars($c['nome']) ?></strong>
              </div>
            </td>
            <td><?= htmlspecialchars($c['telefone'] ?? '—') ?></td>
            <td style="font-size:.83rem;">
              <?php if (!empty($c['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($c['email']) ?>"
                   style="color:var(--ap-red);text-decoration:none;">
                  <?= htmlspecialchars($c['email']) ?>
                </a>
              <?php else: ?>
                <span style="color:var(--ap-gray-lt);">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($c['carro_atual']): ?>
                <span class="ap-badge ap-badge-red"><?= htmlspecialchars($c['carro_atual']) ?></span>
              <?php else: ?>
                <span style="color:var(--ap-gray-lt);">—</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="editar_cliente.php?id=<?= $c['id'] ?>" class="ap-icon-btn edit" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <a href="clientes.php?deletar=<?= $c['id'] ?>"
                   class="ap-icon-btn del" title="Excluir"
                   onclick="return confirm('Excluir <?= htmlspecialchars(addslashes($c['nome'])) ?>?')">
                  <i class="bi bi-trash-fill"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
