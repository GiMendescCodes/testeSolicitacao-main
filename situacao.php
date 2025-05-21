<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'solicitacao');
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;
$dado = null;

if ($id) {
    $id = (int)$id; // segurança básica
    $res = $conn->query("SELECT * FROM dados WHERE id = $id");
    if ($res) {
        $dado = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Situação da Solicitação</title>
</head>
<body>
    <h1>Situação da Solicitação</h1>

    <?php if ($dado): ?>
        <div class="STATUS">
            <?php if ($dado['STATUS'] === 'pendente'): ?>
                <img src="img/pendente.png" alt="Pendente" width="100">
            <?php elseif ($dado['STATUS'] === 'aceito'): ?>
                <img src="img/aceito.png" alt="Aceito" width="100">
            <?php else: ?>
                <img src="img/negado.png" alt="Negado" width="100">
            <?php endif; ?>
            <br>
            <strong>Enviado em:</strong> <?= $dado['data_envio']; ?><br>
            <strong>Nome:</strong> <?= htmlspecialchars($dado['nome']); ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($dado['email']); ?><br>
        </div>
    <?php else: ?>
        <p>Solicitação não encontrada.</p>
    <?php endif; ?>
</body>
</html>
