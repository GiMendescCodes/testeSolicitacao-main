<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT data_escolhida, opcao FROM dados WHERE STATUS = 'aceito'";
$result = $conn->query($sql);

$eventos = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cor = '';

        switch($row['opcao']) {
            case 'Férias':
                $cor = '#ff9f89';
                break;
            case 'Folgas':
                $cor = '#ffd966';
                break;
            case 'Home office':
                $cor = '#9fc5e8';
                break;
            case 'Treinamento':
                $cor = '#b6d7a8';
                break;
            default:
                $cor = '#cccccc';
        }

        $eventos[] = [
            'title' => $row['opcao'],
            'start' => $row['data_escolhida'],
            'color' => $cor
        ];
    }
}

// Feriados exemplo:
$eventos[] = ['title' => 'Natal', 'start' => '2025-12-25', 'color' => '#f4cccc'];
$eventos[] = ['title' => 'Ano Novo', 'start' => '2026-01-01', 'color' => '#f4cccc'];

header('Content-Type: application/json');
echo json_encode($eventos);

$conn->close();
?>
