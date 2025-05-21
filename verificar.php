<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'solicitacao');
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$dados = $conn->query("SELECT * FROM dados WHERE STATUS = 'pendente' ORDER BY data_envio ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verificação de Solicitações</title>
</head>
<body>
    <h1>Solicitações Pendentes</h1>

    <?php if ($dados && $dados->num_rows > 0): ?>
        <?php while ($dado = $dados->fetch_assoc()): ?>
            <div class="dado" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px;">
                <p><strong>Nome:</strong> <?= htmlspecialchars($dado['nome']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($dado['email']); ?></p>
                <p><strong>Enviado em:</strong> <?= $dado['data_envio']; ?></p>

                <form action="acoes.php" method="POST" style="margin-top:10px;">
                    <input type="hidden" name="id" value="<?= $dado['id']; ?>">
                    <button type="submit" name="acao" value="aceitar">Aceitar</button>
                    <button type="submit" name="acao" value="negar">Negar</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Não há solicitações pendentes.</p>
    <?php endif; ?>
</body>
</html>
