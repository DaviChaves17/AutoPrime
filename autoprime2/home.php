<?php
$tituloPagina = 'Home';
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/header.php';
protegerPagina();

$disponiveis = (int)$pdo->query("SELECT COUNT(*) FROM veiculos WHERE status='disponivel'")->fetchColumn();
$alugados    = (int)$pdo->query("SELECT COUNT(*) FROM veiculos WHERE status='alugado'")->fetchColumn();
$manutencao  = (int)$pdo->query("SELECT COUNT(*) FROM veiculos WHERE status='manutencao'")->fetchColumn();
?>

<div class="ap-page">

  <div class="ap-home-header">
    <img src="assets/img/logo.png" alt="AutoPrime">
    <div class="ap-sep">Locadora de Veículos</div>
  </div>

  <div class="row g-4 justify-content-center" style="max-width:860px;margin:0 auto;">

    <div class="col-12 col-sm-4">
      <div class="ap-stat-card">
        <div class="ap-stat-icon green">
          <i class="bi bi-car-front-fill"></i>
        </div>
        <div>
          <div class="ap-stat-label">Carros disponíveis</div>
          <div class="ap-stat-value"><?= $disponiveis ?></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-4">
      <div class="ap-stat-card">
        <div class="ap-stat-icon red">
          <i class="bi bi-key-fill"></i>
        </div>
        <div>
          <div class="ap-stat-label">Carros alugados</div>
          <div class="ap-stat-value"><?= $alugados ?></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-4">
      <div class="ap-stat-card">
        <div class="ap-stat-icon amber">
          <i class="bi bi-wrench-adjustable-circle-fill"></i>
        </div>
        <div>
          <div class="ap-stat-label">Em manutenção</div>
          <div class="ap-stat-value"><?= $manutencao ?></div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
