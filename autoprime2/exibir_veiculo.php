<?php
$tituloPagina = 'Detalhes do Veículo';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$id = (int)($_GET['id'] ?? 0);

// Ação: colocar em manutenção / tirar de manutenção
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];

    if ($acao === 'manutencao') {
        $pdo->prepare("UPDATE veiculos SET status='manutencao' WHERE id=? AND status='disponivel'")
            ->execute([$id]);
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => 'Veículo enviado para manutenção.'];
        header("Location: exibir_veiculo.php?id=$id"); exit;
    }

    if ($acao === 'disponivel') {
        $pdo->prepare("UPDATE veiculos SET status='disponivel' WHERE id=? AND status='manutencao'")
            ->execute([$id]);
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => 'Veículo marcado como disponível.'];
        header("Location: exibir_veiculo.php?id=$id"); exit;
    }

    if ($acao === 'excluir') {
        // Não permite excluir se estiver alugado
        $chk = $pdo->prepare("SELECT status FROM veiculos WHERE id=? LIMIT 1");
        $chk->execute([$id]);
        $status_atual = $chk->fetchColumn();

        if ($status_atual === 'alugado') {
            $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Não é possível excluir: veículo está alugado.'];
            header("Location: exibir_veiculo.php?id=$id"); exit;
        }

        $pdo->prepare("DELETE FROM veiculos WHERE id=?")->execute([$id]);
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => 'Veículo excluído com sucesso.'];
        header('Location: veiculos.php'); exit;
    }
}

$stmt = $pdo->prepare("
    SELECT v.*, c.nome AS nome_cliente, c.telefone AS telefone_cliente, c.email AS email_cliente
    FROM veiculos v
    LEFT JOIN clientes c ON c.id = v.cliente_id
    WHERE v.id = ? LIMIT 1
");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Veículo não encontrado.'];
    header('Location: veiculos.php'); exit;
}

$status_map = [
    'disponivel' => ['label' => 'Disponível', 'class' => 'ap-badge-green'],
    'alugado'    => ['label' => 'Alugado',    'class' => 'ap-badge-red'],
    'manutencao' => ['label' => 'Manutenção', 'class' => 'ap-badge-amber'],
];
$st = $status_map[$v['status']] ?? ['label' => ucfirst($v['status']), 'class' => 'ap-badge-green'];
?>

<div class="ap-page" style="padding-bottom:.5rem;">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-car-front-fill me-2" style="color:var(--ap-red);"></i>Detalhes do Veículo
    </h1>
    <a href="veiculos.php" class="ap-btn ap-btn-outline ap-btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
  </div>
</div>

<div class="ap-modal-page">
  <div class="ap-form-card" style="max-width:600px;">

    <!-- Header -->
    <div class="ap-form-card-header">
      <div class="ap-form-card-title">
        <i class="bi bi-car-front-fill" style="color:var(--ap-red);"></i>
        <?= htmlspecialchars($v['modelo']) ?> — <?= $v['ano'] ?>
      </div>
      <span class="ap-badge <?= $st['class'] ?>"><?= $st['label'] ?></span>
    </div>

    <!-- Body: dados -->
    <div class="ap-form-card-body">
      <div class="ap-vehicle-info" style="grid-template-columns:1fr 1fr 1fr;">
        <div><div class="vl">ID</div><div class="vv">#<?= $v['id'] ?></div></div>
        <div><div class="vl">Modelo</div><div class="vv"><?= htmlspecialchars($v['modelo']) ?></div></div>
        <div><div class="vl">Ano</div><div class="vv"><?= $v['ano'] ?></div></div>
        <div>
          <div class="vl">Cor</div>
          <div class="vv d-flex align-items-center gap-2">
            <span style="width:12px;height:12px;border-radius:50%;background:<?= htmlspecialchars($v['cor_hex'] ?? '#ccc') ?>;border:1px solid rgba(0,0,0,.15);flex-shrink:0;"></span>
            <?= htmlspecialchars($v['cor']) ?>
          </div>
        </div>
        <?php if (!empty($v['placa'])): ?>
        <div><div class="vl">Placa</div><div class="vv"><?= htmlspecialchars($v['placa']) ?></div></div>
        <?php endif; ?>
        <?php if (!empty($v['diaria'])): ?>
        <div>
          <div class="vl">Diária</div>
          <div class="vv" style="color:var(--ap-red);">R$ <?= number_format($v['diaria'], 2, ',', '.') ?></div>
        </div>
        <?php endif; ?>
        <?php if ($v['nome_cliente']): ?>
        <div><div class="vl">Cliente</div><div class="vv"><?= htmlspecialchars($v['nome_cliente']) ?></div></div>
        <div><div class="vl">Telefone</div><div class="vv"><?= htmlspecialchars($v['telefone_cliente'] ?? '—') ?></div></div>
        <?php endif; ?>
        <?php if ($v['data_inicio']): ?>
        <div><div class="vl">Início locação</div><div class="vv"><?= date('d/m/Y', strtotime($v['data_inicio'])) ?></div></div>
        <?php endif; ?>
        <?php if ($v['data_devolucao']): ?>
        <div><div class="vl">Prev. devolução</div><div class="vv"><?= date('d/m/Y', strtotime($v['data_devolucao'])) ?></div></div>
        <?php endif; ?>
      </div>

      <!-- Ações de status / manutenção -->
      <?php if ($v['status'] !== 'alugado'): ?>
      <div style="border-top:1px solid var(--ap-border);padding-top:1.25rem;margin-top:.25rem;">
        <p class="ap-label mb-2">Ações de status</p>
        <div class="d-flex gap-2 flex-wrap">

          <?php if ($v['status'] === 'disponivel'): ?>
            <a href="exibir_veiculo.php?id=<?= $v['id'] ?>&acao=manutencao"
               class="ap-btn ap-btn-sm"
               style="background:#fef3c7;color:#92400e;border:1.5px solid #fde68a;"
               onclick="return confirm('Enviar <?= htmlspecialchars(addslashes($v['modelo'])) ?> para manutenção?')">
              <i class="bi bi-wrench-adjustable me-1"></i>Enviar para Manutenção
            </a>
          <?php endif; ?>

          <?php if ($v['status'] === 'manutencao'): ?>
            <a href="exibir_veiculo.php?id=<?= $v['id'] ?>&acao=disponivel"
               class="ap-btn ap-btn-sm"
               style="background:#dcfce7;color:#15803d;border:1.5px solid #bbf7d0;"
               onclick="return confirm('Marcar <?= htmlspecialchars(addslashes($v['modelo'])) ?> como disponível?')">
              <i class="bi bi-check-circle me-1"></i>Marcar como Disponível
            </a>
          <?php endif; ?>

        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- Footer: ações principais -->
    <div class="ap-form-card-footer" style="justify-content:space-between;flex-wrap:wrap;gap:.75rem;">

      <!-- Esquerda: Excluir -->
      <div>
        <?php if ($v['status'] !== 'alugado'): ?>
        <a href="exibir_veiculo.php?id=<?= $v['id'] ?>&acao=excluir"
           class="ap-btn ap-btn-sm"
           style="background:#fee2e2;color:var(--ap-red);border:1.5px solid #fca5a5;"
           onclick="return confirm('Tem certeza que deseja EXCLUIR o veículo <?= htmlspecialchars(addslashes($v['modelo'])) ?>? Esta ação não pode ser desfeita.')">
          <i class="bi bi-trash-fill me-1"></i>Excluir
        </a>
        <?php else: ?>
        <span class="ap-btn ap-btn-sm" style="background:#f3f4f6;color:var(--ap-gray-lt);cursor:not-allowed;border:1.5px solid var(--ap-border);" title="Não é possível excluir veículo alugado">
          <i class="bi bi-trash me-1"></i>Excluir
        </span>
        <?php endif; ?>
      </div>

      <!-- Direita: Editar + ação de aluguel -->
      <div class="d-flex gap-2">
        <a href="editar_veiculo.php?id=<?= $v['id'] ?>" class="ap-btn ap-btn-black ap-btn-sm">
          <i class="bi bi-pencil-fill me-1"></i>Editar
        </a>

        <?php if ($v['status'] === 'disponivel'): ?>
          <a href="alugar.php?id=<?= $v['id'] ?>" class="ap-btn ap-btn-green ap-btn-sm">
            <i class="bi bi-key me-1"></i>Alugar
          </a>
        <?php elseif ($v['status'] === 'alugado'): ?>
          <a href="desalugar.php?id=<?= $v['id'] ?>" class="ap-btn ap-btn-red ap-btn-sm">
            <i class="bi bi-arrow-return-left me-1"></i>Desalugar
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
