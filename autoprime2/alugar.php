<?php
$tituloPagina = 'Alugar Veículo';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v || $v['status'] !== 'disponivel') {
    $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Veículo não disponível para locação.'];
    header('Location: veiculos.php'); exit;
}

$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC")->fetchAll();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id     = (int)($_POST['cliente_id'] ?? 0);
    $data_inicio    = trim($_POST['data_inicio'] ?? '');
    $data_devolucao = trim($_POST['data_devolucao'] ?? '');

    if (!$cliente_id)                        $erro = 'Selecione um cliente.';
    elseif (!$data_inicio)                   $erro = 'Informe a data de início.';
    elseif (!$data_devolucao)                $erro = 'Informe a data de devolução.';
    elseif ($data_devolucao <= $data_inicio) $erro = 'A data de devolução deve ser posterior à de início.';

    if (!$erro) {
        $pdo->prepare("UPDATE veiculos SET status='alugado', cliente_id=?, data_inicio=?, data_devolucao=? WHERE id=?")
            ->execute([$cliente_id, $data_inicio, $data_devolucao, $id]);
        try {
            $pdo->prepare("INSERT INTO locacoes (veiculo_id, cliente_id, data_inicio, data_devolucao) VALUES (?,?,?,?)")
                ->execute([$id, $cliente_id, $data_inicio, $data_devolucao]);
        } catch (\Exception $e) {}

        $nc = $pdo->prepare("SELECT nome FROM clientes WHERE id=? LIMIT 1");
        $nc->execute([$cliente_id]);
        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => "Veículo {$v['modelo']} alugado para " . $nc->fetchColumn() . "!"];
        header('Location: veiculos.php'); exit;
    }
}
?>

<div class="ap-page" style="padding-bottom:.5rem;">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-key-fill me-2" style="color:var(--ap-green);"></i>Registrar Locação
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
        <i class="bi bi-car-front-fill" style="color:var(--ap-green);"></i>
        <?= htmlspecialchars($v['modelo']) ?> — <?= $v['ano'] ?>
      </div>
      <span class="ap-badge ap-badge-green">Disponível</span>
    </div>

    <div class="ap-form-card-body">

      <div class="ap-vehicle-info">
        <div><div class="vl">Modelo</div><div class="vv"><?= htmlspecialchars($v['modelo']) ?></div></div>
        <div><div class="vl">Ano</div><div class="vv"><?= $v['ano'] ?></div></div>
        <div><div class="vl">Cor</div><div class="vv"><?= htmlspecialchars($v['cor']) ?></div></div>
        <?php if (!empty($v['placa'])): ?>
        <div><div class="vl">Placa</div><div class="vv"><?= htmlspecialchars($v['placa']) ?></div></div>
        <?php endif; ?>
        <?php if (!empty($v['diaria'])): ?>
        <div>
          <div class="vl">Diária</div>
          <div class="vv" style="color:var(--ap-red);">R$ <?= number_format($v['diaria'], 2, ',', '.') ?></div>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($erro): ?>
      <div class="ap-alert ap-alert-danger">
        <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <?= htmlspecialchars($erro) ?>
      </div>
      <?php endif; ?>

      <form method="POST" novalidate id="form-alugar">
        <div class="mb-3">
          <label class="ap-label" for="cliente_id">Cliente *</label>
          <select name="cliente_id" id="cliente_id" class="ap-select" required>
            <option value="">— Selecione um cliente —</option>
            <?php foreach ($clientes as $c): ?>
              <option value="<?= $c['id'] ?>" <?= (($_POST['cliente_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="ap-label" for="data_inicio">Data de Início *</label>
            <input type="date" name="data_inicio" id="data_inicio" class="ap-input"
              value="<?= htmlspecialchars($_POST['data_inicio'] ?? date('Y-m-d')) ?>"
              min="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-6">
            <label class="ap-label" for="data_devolucao">Prev. Devolução *</label>
            <input type="date" name="data_devolucao" id="data_devolucao" class="ap-input"
              value="<?= htmlspecialchars($_POST['data_devolucao'] ?? '') ?>"
              min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
          </div>
        </div>

        <div id="preview-dias" style="display:none;">
          <div class="ap-summary-box">
            <div>
              <div class="s-lbl">Período</div>
              <div class="s-val" id="txt-dias">—</div>
            </div>
            <?php if (!empty($v['diaria'])): ?>
            <div style="text-align:right;">
              <div class="s-lbl">Total estimado</div>
              <div class="s-val accent" id="txt-total">—</div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </form>

    </div>

    <div class="ap-form-card-footer">
      <a href="veiculos.php" class="ap-btn ap-btn-outline">Cancelar</a>
      <button type="submit" form="form-alugar" class="ap-btn ap-btn-green">
        <i class="bi bi-check-circle-fill me-1"></i>Confirmar Aluguel
      </button>
    </div>

  </div>
</div>

<script>
const diaria = <?= (float)($v['diaria'] ?? 0) ?>;
function calcDias() {
  const di  = document.getElementById('data_inicio').value;
  const dd  = document.getElementById('data_devolucao').value;
  const box = document.getElementById('preview-dias');
  if (!di || !dd || dd <= di) { box.style.display = 'none'; return; }
  const dias = Math.round((new Date(dd) - new Date(di)) / 86400000);
  document.getElementById('txt-dias').textContent = dias + (dias === 1 ? ' dia' : ' dias');
  if (diaria > 0)
    document.getElementById('txt-total').textContent =
      (dias * diaria).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  box.style.display = 'block';
}
document.getElementById('data_inicio').addEventListener('change', calcDias);
document.getElementById('data_devolucao').addEventListener('change', calcDias);
calcDias();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
