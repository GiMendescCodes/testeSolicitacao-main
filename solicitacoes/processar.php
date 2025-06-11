<?php
header('Content-Type: application/json');
session_start();

$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão']);
    exit;
}

$idFuncionario = $_SESSION['id_funcionario'] ?? null;
$data = $_POST['data'] ?? '';
$mensagem = $_POST['mensagem'] ?? '';
$opcao = $_POST['opcao'] ?? '';

if (!$data || !$mensagem || !$opcao || !$idFuncionario) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO dados (id_funcionario, data_escolhida, opcao, mensagem, status) VALUES (?, ?, ?, ?, 'pendente')");
$stmt->bind_param("isss", $idFuncionario, $data, $opcao, $mensagem);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'data' => $data,
        'opcao' => $opcao,
        'status' => 'pendente',
        'id' => $id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar solicitação']);
}
$stmt->close();
$conn->close();
