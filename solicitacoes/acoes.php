<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'solicitacao');
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $acao = $_POST['acao'] ?? '';

    if ($id > 0 && in_array($acao, ['aceitar', 'negar'])) {
        $novo_status = $acao === 'aceitar' ? 'aceito' : 'negado';

        $stmt = $conn->prepare("UPDATE dados SET STATUS = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_status, $id);
        $stmt->execute();
    }
}

// Redireciona de volta para verificar.php
header('Location: verificar.php');
exit;
