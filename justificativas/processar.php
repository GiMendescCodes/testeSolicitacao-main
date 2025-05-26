<?php
$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$data = $_POST['data'] ?? null;
$mensagem = $_POST['mensagem'] ?? null;
$opcao = $_POST['opcao'] ?? null;

if (!$data || !$mensagem || !$opcao) {
    echo "<script>alert('Preencha todos os campos obrigatórios.'); window.history.back();</script>";
    exit;
}


$targetFilePath = null;

if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $filename = basename($_FILES['arquivo']['name']);
    $targetFilePath = $targetDir . uniqid() . "_" . $filename;

    if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $targetFilePath)) {
        die("Erro ao salvar o arquivo.");
    }
}

$status = 'pendente';

$arquivoDB = $targetFilePath ?? '';

$stmt = $conn->prepare("INSERT INTO justificativas (data_escolhida, mensagem, opcao, arquivo, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $data, $mensagem, $opcao, $arquivoDB, $status);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php"); // ajuste o redirecionamento para sua página
    exit;
} else {
    die("Erro ao salvar justificativa: " . $conn->error);
}
?>
