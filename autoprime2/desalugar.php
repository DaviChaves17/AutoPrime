<?php
$tituloPagina = 'Devolução de Veículo';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT v.*, c.nome AS nome_cliente, c.telefone AS telefone_cliente
    FROM veiculos v
    LEFT JOIN clientes c ON c.id = v.cliente_id
    WHERE v.id = ? LIMIT 1
");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v || $v['status'] !== 'alugado') {
    $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Veículo não está alugado.'];
    header('Location: veiculos.php'); exit;
}

$dias = 0; $valor = 0; $atraso = false;
if ($v['data_inicio']) {
    $dias  = max(1, (int)(new DateTime($v['data_inicio']))->diff(new DateTime())->days);
    $valor = $dias * (float)($v['diaria'] ?? 0);
}
if ($v['data_devolucao'] && $v['data_devolucao'] < date('Y-m-d')) $atraso = true;

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['confirmar'])) {
        $erro = 'Marque a caixa de confirmação para prosseguir.';
    } else {
        $pdo->prepare("UPDATE veiculos SET status='disponivel', cliente_id=NULL, data_inicio=NULL, data_devolucao=NULL WHERE id=?")
            ->execute([$id]);
        try {
            $pdo->prepare("UPDATE locacoes SET status='finalizado', data_devolucao_real=CURDATE() WHERE veiculo_id=? AND status='ativo'")
                ->execute([$id]);
        } catch (\Exception $e) {}
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => "Veículo {$v['modelo']} devolvido com sucesso!"];
        header('Location: veiculos.php'); exit;
    }
}
?>

<div class="ap-page" style="padding-bottom:.5rem;">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-arrow-return-left me-2" style="color:var(--ap-red);"></i>Devolução de Veículo
    </h1>
    <a href="veiculos.php" class="ap-btn ap-btn-outline ap-btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
  </div>
</div>

<div class="ap-modal-page">
  <div class="ap-form-card">

    <div class="ap-form-card-header">
      <div class="ap-form-card-title">
        <i class="bi bi-car-front-fill" style="color:var(--ap-red);"></i>
        <?= htmlspecialchars($v['modelo']) ?> — <?= $v['ano'] ?>
      </div>
      <span class="ap-badge ap-badge-red">Alugado</span>
    </div>

    <div class="ap-form-card-body">

      <div class="ap-vehicle-info">
        <div><div class="vl">Modelo</div><div class="vv"><?= htmlspecialchars($v['modelo']) ?></div></div>
        <div><div class="vl">Ano / Cor</div><div class="vv"><?= $v['ano'] ?> · <?= htmlspecialchars($v['cor']) ?></div></div>
        <?php if ($v['nome_cliente']): ?>
        <div><div class="vl">Cliente</div><div class="vv"><?= htmlspecialchars($v['nome_cliente']) ?></div></div>
        <?php endif; ?>
        <?php if ($v['telefone_cliente']): ?>
        <div><div class="vl">Telefone</div><div class="vv"><?= htmlspecialchars($v['telefone_cliente']) ?></div></div>
        <?php endif; ?>
        <?php if ($v['data_inicio']): ?>
        <div><div class="vl">Início da locação</div><div class="vv"><?= date('d/m/Y', strtotime($v['data_inicio'])) ?></div></div>
        <?php endif; ?>
        <?php if ($v['data_devolucao']): ?>
        <div>
          <div class="vl">Prev. devolução</div>
          <div class="vv <?= $atraso ? 'text-danger fw-bold' : '' ?>">
            <?= date('d/m/Y', strtotime($v['data_devolucao'])) ?>
            <?= $atraso ? ' <span class="ap-badge ap-badge-red ms-1">Em atraso</span>' : '' ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($dias > 0): ?>
      <div class="ap-summary-box">
        <div>
          <div class="s-lbl">Dias locados</div>
          <div class="s-val"><?= $dias ?> <?= $dias === 1 ? 'dia' : 'dias' ?></div>
        </div>
        <?php if ($valor > 0): ?>
        <div style="text-align:right;">
          <div class="s-lbl">Total estimado</div>
          <div class="s-val accent">R$ <?= number_format($valor, 2, ',', '.') ?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if ($atraso): ?>
      <div class="ap-alert ap-alert-warning">
        <i class="bi bi-clock-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <div>Este veículo está com devolução em <strong>atraso</strong>. Verifique cobranças adicionais.</div>
      </div>
      <?php endif; ?>

      <div class="ap-alert ap-alert-danger">
        <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <div>Esta ação marcará o veículo como <strong>disponível</strong> e encerrará a locação. A operação <strong>não pode ser desfeita</strong>.</div>
      </div>

      <?php if ($erro): ?>
      <div class="ap-alert ap-alert-danger">
        <i class="bi bi-x-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <?= htmlspecialchars($erro) ?>
      </div>
      <?php endif; ?>

      <form method="POST" novalidate id="form-desalugar">
        <label class="ap-confirm-label" id="confirm-wrap">
          <input type="checkbox" name="confirmar" value="1" id="cb-confirmar"
            onchange="toggleBtn(this)" <?= isset($_POST['confirmar']) ? 'checked' : '' ?>>
          <span>
            Confirmo que o veículo <strong><?= htmlspecialchars($v['modelo']) ?></strong>
            foi devolvido em boas condições.
          </span>
        </label>
      </form>

    </div>

    <div class="ap-form-card-footer">
      <a href="veiculos.php" class="ap-btn ap-btn-outline">Cancelar</a>
      <button type="submit" form="form-desalugar" class="ap-btn ap-btn-red"
        id="btn-confirmar"
        <?= !isset($_POST['confirmar']) ? 'disabled style="opacity:.5;cursor:not-allowed;"' : '' ?>>
        <i class="bi bi-check-circle-fill me-1"></i>Confirmar Devolução
      </button>
    </div>

  </div>
</div>

<script>
function toggleBtn(cb) {
  const btn  = document.getElementById('btn-confirmar');
  const wrap = document.getElementById('confirm-wrap');
  btn.disabled      = !cb.checked;
  btn.style.opacity = cb.checked ? '1' : '.5';
  btn.style.cursor  = cb.checked ? 'pointer' : 'not-allowed';
  wrap.classList.toggle('checked', cb.checked);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
