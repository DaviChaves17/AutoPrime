<?php
$tituloPagina = 'Editar Veículo';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'msg' => 'Veículo não encontrado.'];
    header('Location: veiculos.php'); exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo  = trim($_POST['modelo'] ?? '');
    $ano     = trim($_POST['ano'] ?? '');
    $cor     = trim($_POST['cor'] ?? '');
    $cor_hex = trim($_POST['cor_hex'] ?? '#cccccc');
    $placa   = trim($_POST['placa'] ?? '');
    $diaria  = str_replace(',', '.', trim($_POST['diaria'] ?? '0'));

    if (!$modelo)      $erro = 'O modelo é obrigatório.';
    elseif (!$ano)     $erro = 'O ano é obrigatório.';
    elseif (!$cor)     $erro = 'A cor é obrigatória.';
    elseif ((int)$ano < 1900 || (int)$ano > (int)date('Y') + 1)
                       $erro = 'Informe um ano válido.';

    if (!$erro) {
        $pdo->prepare("
            UPDATE veiculos SET modelo=?, ano=?, cor=?, cor_hex=?, placa=?, diaria=? WHERE id=?
        ")->execute([$modelo, (int)$ano, $cor, $cor_hex, $placa ?: null, (float)$diaria, $id]);

        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => "Veículo {$modelo} atualizado!"];
        header("Location: exibir_veiculo.php?id=$id"); exit;
    }

    // Mantém valores do POST em caso de erro
    $v = array_merge($v, $_POST);
}

$cores_hex = [
    'Preto'    => '#1a1a1a',
    'Branco'   => '#f9fafb',
    'Prata'    => '#9ca3af',
    'Cinza'    => '#6b7280',
    'Vermelho' => '#dc2626',
    'Azul'     => '#1d4ed8',
    'Amarelo'  => '#fbbf24',
    'Verde'    => '#16a34a',
    'Laranja'  => '#f97316',
    'Marrom'   => '#92400e',
    'Bege'     => '#d6cfb0',
    'Vinho'    => '#7f1d1d',
    'Outra'    => '#cccccc',
];
?>

<div class="ap-page" style="padding-bottom:.5rem;">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-pencil-fill me-2" style="color:var(--ap-red);"></i>Editar Veículo
    </h1>
    <a href="exibir_veiculo.php?id=<?= $id ?>" class="ap-btn ap-btn-outline ap-btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
  </div>
</div>

<div class="ap-modal-page">
  <div class="ap-form-card">

    <div class="ap-form-card-header">
      <div class="ap-form-card-title">
        <i class="bi bi-car-front-fill" style="color:var(--ap-red);"></i>
        Editando: <?= htmlspecialchars($v['modelo']) ?>
      </div>
      <span class="ap-id-badge">#<?= $v['id'] ?></span>
    </div>

    <div class="ap-form-card-body">

      <?php if ($erro): ?>
      <div class="ap-alert ap-alert-danger">
        <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <?= htmlspecialchars($erro) ?>
      </div>
      <?php endif; ?>

      <form method="POST" novalidate id="form-editar-veiculo">

        <div class="mb-3">
          <label class="ap-label" for="modelo">Modelo *</label>
          <input type="text" name="modelo" id="modelo" class="ap-input"
            placeholder="Ex: HB20, Corolla..."
            value="<?= htmlspecialchars($v['modelo']) ?>" required>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="ap-label" for="ano">Ano *</label>
            <input type="number" name="ano" id="ano" class="ap-input"
              value="<?= htmlspecialchars($v['ano']) ?>"
              min="1900" max="<?= date('Y') + 1 ?>" required>
          </div>
          <div class="col-6">
            <label class="ap-label" for="placa">Placa</label>
            <input type="text" name="placa" id="placa" class="ap-input"
              placeholder="ABC-1234"
              value="<?= htmlspecialchars($v['placa'] ?? '') ?>"
              maxlength="10">
          </div>
        </div>

        <div class="mb-3">
          <label class="ap-label" for="cor">Cor *</label>
          <div class="d-flex gap-2">
            <select name="cor" id="cor" class="ap-select" onchange="atualizarCor(this)" required style="flex:1;">
              <option value="">— Selecione a cor —</option>
              <?php foreach ($cores_hex as $nome => $hex): ?>
                <option value="<?= $nome ?>"
                  data-hex="<?= $hex ?>"
                  <?= ($v['cor'] === $nome) ? 'selected' : '' ?>>
                  <?= $nome ?>
                </option>
              <?php endforeach; ?>
              <!-- Opção para cor atual se não estiver na lista -->
              <?php if (!array_key_exists($v['cor'], $cores_hex)): ?>
                <option value="<?= htmlspecialchars($v['cor']) ?>"
                  data-hex="<?= htmlspecialchars($v['cor_hex'] ?? '#ccc') ?>"
                  selected>
                  <?= htmlspecialchars($v['cor']) ?>
                </option>
              <?php endif; ?>
            </select>
            <div id="cor-preview"
              style="width:42px;height:42px;border-radius:var(--ap-radius);
                     border:1.5px solid var(--ap-border);
                     background:<?= htmlspecialchars($v['cor_hex'] ?? '#f9f5f5') ?>;
                     flex-shrink:0;transition:background .2s;">
            </div>
          </div>
          <input type="hidden" name="cor_hex" id="cor_hex" value="<?= htmlspecialchars($v['cor_hex'] ?? '#cccccc') ?>">
        </div>

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="ap-label" for="diaria">Diária (R$)</label>
            <input type="number" name="diaria" id="diaria" class="ap-input"
              placeholder="120,00"
              value="<?= htmlspecialchars($v['diaria'] ?? '') ?>"
              min="0" step="0.01">
          </div>
        </div>

        <?php if ($v['status'] === 'alugado'): ?>
        <div class="ap-alert ap-alert-warning" style="margin-bottom:0;">
          <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
          <div>Este veículo está <strong>alugado</strong>. O status não pode ser alterado aqui.</div>
        </div>
        <?php endif; ?>

      </form>
    </div>

    <div class="ap-form-card-footer">
      <a href="exibir_veiculo.php?id=<?= $id ?>" class="ap-btn ap-btn-outline">Cancelar</a>
      <button type="submit" form="form-editar-veiculo" class="ap-btn ap-btn-red">
        <i class="bi bi-check-circle-fill me-1"></i>Salvar Alterações
      </button>
    </div>

  </div>
</div>

<script>
function atualizarCor(sel) {
  const hex = sel.options[sel.selectedIndex].dataset.hex || '#f9f5f5';
  document.getElementById('cor_hex').value = hex;
  document.getElementById('cor-preview').style.background = hex;
}
window.addEventListener('DOMContentLoaded', function () {
  const sel = document.getElementById('cor');
  if (sel.value) atualizarCor(sel);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
