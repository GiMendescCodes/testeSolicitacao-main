<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se veio a ação para processar (via AJAX)
if (isset($_GET['acao'], $_GET['id'])) {
    $acao = $_GET['acao'];
    $id = (int) $_GET['id'];

    if ($acao === 'aceitar' || $acao === 'negar') {
        $novoStatus = ($acao === 'aceitar') ? 'aceito' : 'negado';

        $stmt = $conn->prepare("UPDATE dados SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $novoStatus, $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo "Status atualizado para $novoStatus";
        } else {
            http_response_code(500);
            echo "Erro ao atualizar status";
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo "Ação inválida";
    }

    $conn->close();
    exit; // para não carregar o restante do HTML
}

// Código para mostrar as solicitações e botões de ação
$sql = "SELECT id, data_escolhida, mensagem, opcao, status FROM dados ORDER BY id DESC";
$result = $conn->query($sql);

echo '<h2>Verificação de Solicitações</h2>';

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $cardId = "card_" . $row['id'];
            echo "<div class='card' id='$cardId'>";
            echo "<p><strong>Data:</strong> " . htmlspecialchars($row['data_escolhida'] ?? '') . "</p>";
            echo "<p><strong>Opção:</strong> " . htmlspecialchars($row['opcao'] ?? '') . "</p>";
            echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($row['mensagem'] ?? '') . "</p>";
            echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status'] ?? '') . "</p>";

            echo "<div>";
            echo "<button class='btn' onclick=\"processarAcao('aceitar', " . $row['id'] . ", '$cardId')\">Aceitar</button>";
            echo "<button class='btn' onclick=\"processarAcao('negar', " . $row['id'] . ", '$cardId')\">Negar</button>";
            echo "</div>";

            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma solicitação para verificar.</p>";
    }
} else {
    echo "<p>Erro ao consultar solicitações: " . htmlspecialchars($conn->error) . "</p>";
}


$conn->close();
?>

<script>
function processarAcao(acao, id, cardId) {
    if (!confirm('Tem certeza que deseja ' + acao + ' esta solicitação?')) return;

    fetch('verificar.php?acao=' + acao + '&id=' + id)
        .then(response => {
            if (response.ok) {
                // Aqui removemos o card da tela, mas você pode fazer atualizar status ou outra coisa
                document.getElementById(cardId).remove();
            } else {
                alert('Erro ao processar a ação.');
            }
        })
        .catch(error => {
            alert('Erro de conexão.');
            console.error(error);
        });
}
</script>

<style>
.card {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
    background-color: #fafafa;
}
.btn {
    margin-right: 10px;
    padding: 6px 12px;
    cursor: pointer;
}
</style>
