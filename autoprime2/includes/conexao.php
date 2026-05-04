<?php
// Conexão com o banco MySQL do Laragon
// Banco: autoprime | Porta: 3306 | Usuário: root | Senha: vazia
$host    = 'localhost';
$porta   = '3306';
$banco   = 'autoprime';
$usuario = 'root';
$senha   = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:2rem;background:#fee2e2;color:#991b1b;
        border-left:4px solid #cc0000;margin:2rem;border-radius:8px;">
        <strong>Erro ao conectar no banco autoprime:</strong><br>
        ' . htmlspecialchars($e->getMessage()) . '
        <br><br><small>Verifique se o MySQL do Laragon está rodando e se você executou o <b>banco.sql</b>.</small>
    </div>');
}
