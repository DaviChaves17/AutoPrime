<?php
$tituloPagina = 'Veículos';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$veiculos = $pdo->query("
    SELECT v.*, c.nome AS nome_cliente
    FROM veiculos v
    LEFT JOIN clientes c ON c.id = v.cliente_id
    ORDER BY v.id ASC
")->fetchAll();

$status_map = [
    'disponivel' => ['label' => 'Disponível', 'class' => 'ap-badge-green'],
    'alugado'    => ['label' => 'Alugado',    'class' => 'ap-badge-red'],
    'manutencao' => ['label' => 'Manutenção', 'class' => 'ap-badge-amber'],
];
?>

<div class="ap-page">
  <div class="ap-section-header">
    <h1 class="ap-section-title">
      <i class="bi bi-car-front me-2" style="color:var(--ap-red);"></i>Veículos
    </h1>
    <a href="novo_veiculo.php" class="ap-btn ap-btn-red ap-btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Cadastrar Veículo
    </a>
  </div>

  <div class="ap-table-wrap">
    <table class="ap-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Modelo</th>
          <th>Ano</th>
          <th>Cor</th>
          <th>Status</th>
          <th>Cliente</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($veiculos)): ?>
          <tr><td colspan="7"><div class="ap-empty">Nenhum veículo cadastrado.</div></td></tr>
        <?php else: ?>
          <?php foreach ($veiculos as $v):
            $st = $status_map[$v['status']] ?? ['label' => ucfirst($v['status']), 'class' => 'ap-badge-green'];
          ?>
          <tr>
            <td><span class="ap-id-badge">#<?= $v['id'] ?></span></td>
            <td><strong><?= htmlspecialchars($v['modelo']) ?></strong></td>
            <td><?= $v['ano'] ?></td>
            <td>
              <span class="d-flex align-items-center gap-2">
                <span style="width:10px;height:10px;border-radius:50%;
                  background:<?= htmlspecialchars($v['cor_hex'] ?? '#ccc') ?>;
                  border:1px solid rgba(0,0,0,.12);display:inline-block;flex-shrink:0;">
                </span>
                <?= htmlspecialchars($v['cor']) ?>
              </span>
            </td>
            <td>
              <span class="ap-badge <?= $st['class'] ?>"><?= $st['label'] ?></span>
            </td>
            <td>
              <?php if ($v['nome_cliente']): ?>
                <div class="d-flex align-items-center gap-2">
                  <div class="ap-avatar"><?= strtoupper(mb_substr($v['nome_cliente'], 0, 2)) ?></div>
                  <span><?= htmlspecialchars($v['nome_cliente']) ?></span>
                </div>
              <?php else: ?>
                <span style="color:var(--ap-gray-lt);">—</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <a href="exibir_veiculo.php?id=<?= $v['id'] ?>" class="ap-btn ap-btn-black ap-btn-sm">
                  <i class="bi bi-eye"></i> Exibir
                </a>
                <?php if ($v['status'] === 'disponivel'): ?>
                  <a href="alugar.php?id=<?= $v['id'] ?>" class="ap-btn ap-btn-green ap-btn-sm">
                    <i class="bi bi-key"></i> Alugar
                  </a>
                <?php elseif ($v['status'] === 'alugado'): ?>
                  <a href="desalugar.php?id=<?= $v['id'] ?>" class="ap-btn ap-btn-red ap-btn-sm">
                    <i class="bi bi-arrow-return-left"></i> Desalugar
                  </a>
                <?php endif; ?>
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
