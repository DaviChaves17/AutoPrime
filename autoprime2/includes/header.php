<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoPrime — <?= $tituloPagina ?? 'Painel' ?></title>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- AutoPrime CSS -->
  <link href="assets/css/autoprime.css" rel="stylesheet">
</head>
<body>

<?php
// Flash message (feedback de ação)
if (!empty($_SESSION['flash'])):
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
?>
<div class="ap-toast ap-toast--<?= $flash['tipo'] ?>">
  <i class="bi bi-<?= $flash['tipo'] === 'sucesso' ? 'check-circle-fill' : 'exclamation-circle-fill' ?>"></i>
  <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<nav class="ap-navbar navbar">
  <div class="container-fluid px-0">

    <a class="navbar-brand me-4" href="home.php">
      <div class="logo-nav-wrap">
        <img src="assets/img/logo_nav.png" alt="AutoPrime">
      </div>
    </a>

    <ul class="navbar-nav flex-row gap-1 me-auto">
      <li class="nav-item">
        <a class="nav-link <?= ativo('home.php') ?>" href="home.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ativo('veiculos.php') ?>" href="veiculos.php">Veículos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ativo('clientes.php') ?>" href="clientes.php">Clientes</a>
      </li>
    </ul>

    <div class="d-flex align-items-center gap-3">
      <span class="ap-navbar-user">
        <i class="bi bi-person-circle me-1"></i>
        <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin') ?>
      </span>
      <a href="logout.php" class="ap-btn-logout">
        <i class="bi bi-box-arrow-right me-1"></i>Sair
      </a>
    </div>

  </div>
</nav>
