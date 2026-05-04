<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function protegerPagina() {
    if (empty($_SESSION['logado'])) {
        header('Location: index.php');
        exit;
    }
}

function ativo($pagina) {
    return basename($_SERVER['PHP_SELF']) === $pagina ? 'active' : '';
}
