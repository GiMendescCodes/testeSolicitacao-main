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
            case 'Folga':
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


header('Content-Type: application/json');
echo json_encode($eventos);

$conn->close();
?>
