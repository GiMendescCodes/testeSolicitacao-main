<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$data = $_POST['data'];
$mensagem = $_POST['mensagem'];
$opcao = $_POST['opcao'];

// Corrigido: tabela "dados" e inserção de STATUS 'pendente'
$sql = "INSERT INTO dados (data_escolhida, mensagem, opcao, STATUS) VALUES (?, ?, ?, 'pendente')";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação: " . $conn->error);
}

$stmt->bind_param("sss", $data, $mensagem, $opcao);

if ($stmt->execute()) {
    header("Location: index.php?sucesso=1");
    exit;
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
