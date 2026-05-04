<?php
$tituloPagina = 'Novo Veículo';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = trim($_POST['modelo'] ?? '');
    $ano    = trim($_POST['ano'] ?? '');
    $cor    = trim($_POST['cor'] ?? '');
    $cor_hex = trim($_POST['cor_hex'] ?? '#cccccc');
    $placa  = trim($_POST['placa'] ?? '');
    $diaria = str_replace(',', '.', trim($_POST['diaria'] ?? '0'));
    $status = $_POST['status'] ?? 'disponivel';

    if (!$modelo)         $erro = 'O modelo é obrigatório.';
    elseif (!$ano)        $erro = 'O ano é obrigatório.';
    elseif (!$cor)        $erro = 'A cor é obrigatória.';
    elseif ((int)$ano < 1900 || (int)$ano > (int)date('Y') + 1)
                          $erro = 'Informe um ano válido.';

    if (!$erro) {
        $pdo->prepare("
            INSERT INTO veiculos (modelo, ano, cor, cor_hex, placa, diaria, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ")->execute([$modelo, (int)$ano, $cor, $cor_hex, $placa ?: null, (float)$diaria, $status]);

        $_SESSION['flash'] = ['tipo' => 'sucesso', 'msg' => "Veículo {$modelo} cadastrado com sucesso!"];
        header('Location: veiculos.php'); exit;
    }
}

$cores_hex = [
    'Preto'   => '#1a1a1a',
    'Branco'  => '#f9fafb',
    'Prata'   => '#9ca3af',
    'Cinza'   => '#6b7280',
    'Vermelho'=> '#dc2626',
    'Azul'    => '#1d4ed8',
    'Amarelo' => '#fbbf24',
    'Verde'   => '#16a34a',
    'Laranja' => '#f97316',
    'Marrom'  => '#92400e',
    'Bege'    => '#d6cfb0',
    'Vinho'   => '#7f1d1d',
    'Outra'   => '#cccccc',
];
?>

<div class="ap-page" style="padding-bottom:.5rem;">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-car-front-fill me-2" style="color:var(--ap-red);"></i>Novo Veículo
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
        <i class="bi bi-car-front-fill" style="color:var(--ap-red);"></i>Dados do Veículo
      </div>
    </div>

    <div class="ap-form-card-body">

      <?php if ($erro): ?>
      <div class="ap-alert ap-alert-danger">
        <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
        <?= htmlspecialchars($erro) ?>
      </div>
      <?php endif; ?>

      <form method="POST" novalidate id="form-veiculo">

        <!-- Modelo -->
        <div class="mb-3">
          <label class="ap-label" for="modelo">Modelo *</label>
          <input type="text" name="modelo" id="modelo" class="ap-input"
            placeholder="Ex: HB20, Corolla, Civic..."
            value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>" required>
        </div>

        <!-- Ano + Placa -->
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="ap-label" for="ano">Ano *</label>
            <input type="number" name="ano" id="ano" class="ap-input"
              placeholder="<?= date('Y') ?>"
              value="<?= htmlspecialchars($_POST['ano'] ?? '') ?>"
              min="1900" max="<?= date('Y') + 1 ?>" required>
          </div>
          <div class="col-6">
            <label class="ap-label" for="placa">Placa</label>
            <input type="text" name="placa" id="placa" class="ap-input"
              placeholder="ABC-1234"
              value="<?= htmlspecialchars($_POST['placa'] ?? '') ?>"
              maxlength="10">
          </div>
        </div>

        <!-- Cor -->
        <div class="mb-3">
          <label class="ap-label" for="cor">Cor *</label>
          <div class="d-flex gap-2">
            <select name="cor" id="cor" class="ap-select" onchange="atualizarCor(this)" required
              style="flex:1;">
              <option value="">— Selecione a cor —</option>
              <?php foreach ($cores_hex as $nome => $hex): ?>
                <option value="<?= $nome ?>"
                  data-hex="<?= $hex ?>"
                  <?= (($_POST['cor'] ?? '') === $nome) ? 'selected' : '' ?>>
                  <?= $nome ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div id="cor-preview"
              style="width:42px;height:42px;border-radius:var(--ap-radius);border:1.5px solid var(--ap-border);
                     background:<?= htmlspecialchars($_POST['cor_hex'] ?? '#f9f5f5') ?>;flex-shrink:0;transition:background .2s;">
            </div>
          </div>
          <input type="hidden" name="cor_hex" id="cor_hex" value="<?= htmlspecialchars($_POST['cor_hex'] ?? '#cccccc') ?>">
        </div>

        <!-- Diária + Status -->
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="ap-label" for="diaria">Diária (R$)</label>
            <input type="number" name="diaria" id="diaria" class="ap-input"
              placeholder="120,00"
              value="<?= htmlspecialchars($_POST['diaria'] ?? '') ?>"
              min="0" step="0.01">
          </div>
          <div class="col-6">
            <label class="ap-label" for="status">Status</label>
            <select name="status" id="status" class="ap-select">
              <option value="disponivel" <?= (($_POST['status'] ?? 'disponivel') === 'disponivel') ? 'selected' : '' ?>>Disponível</option>
              <option value="manutencao" <?= (($_POST['status'] ?? '') === 'manutencao') ? 'selected' : '' ?>>Em Manutenção</option>
            </select>
          </div>
        </div>

      </form>
    </div>

    <div class="ap-form-card-footer">
      <a href="veiculos.php" class="ap-btn ap-btn-outline">Cancelar</a>
      <button type="submit" form="form-veiculo" class="ap-btn ap-btn-red">
        <i class="bi bi-check-circle-fill me-1"></i>Cadastrar Veículo
      </button>
    </div>

  </div>
</div>

<script>
function atualizarCor(sel) {
  const opt = sel.options[sel.selectedIndex];
  const hex = opt.dataset.hex || '#f9f5f5';
  document.getElementById('cor_hex').value = hex;
  document.getElementById('cor-preview').style.background = hex;
}
// Inicializa o preview se já houver valor selecionado
window.addEventListener('DOMContentLoaded', function() {
  const sel = document.getElementById('cor');
  if (sel.value) atualizarCor(sel);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
