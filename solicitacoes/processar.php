<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$data = $_POST['data'];
$mensagem = $_POST['mensagem'];
$opcao = $_POST['opcao'];

$arquivo = null;
if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
    $pasta = 'uploads/';
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $nomeArquivo = basename($_FILES['arquivo']['name']);
    $destino = $pasta . $nomeArquivo;

    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
        $arquivo = $nomeArquivo;
    } else {
        die("Erro ao enviar o arquivo.");
    }
}

$status = 'pendente';

if ($arquivo !== null) {
    $sql = "INSERT INTO dados (data_escolhida, mensagem, opcao, STATUS, arquivo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação: " . $conn->error);
    }

    $stmt->bind_param("sssss", $data, $mensagem, $opcao, $status, $arquivo);
} else {
    $sql = "INSERT INTO dados (data_escolhida, mensagem, opcao, STATUS) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação: " . $conn->error);
    }

    $stmt->bind_param("ssss", $data, $mensagem, $opcao, $status);
}

if ($stmt->execute()) {
    header("Location: index.php?sucesso=1");
    exit;
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
