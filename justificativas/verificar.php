<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT id, data_escolhida, mensagem, opcao FROM justificativas ORDER BY id DESC";
$result = $conn->query($sql);

echo '<style>
.card {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin: 15px auto;
    max-width: 400px;
    background: #fff;
    box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
}
.btn {
    display: inline-block;
    padding: 5px 10px;
    margin-right: 10px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}
</style>';

echo '<h2>Verificação de Justificativas</h2>';

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $cardId = "card_" . $row['id'];
            echo "<div class='card' id='$cardId'>";
            echo "<p><strong>Data:</strong> " . htmlspecialchars($row['data_escolhida']) . "</p>";
            echo "<p><strong>Opção:</strong> " . htmlspecialchars($row['opcao']) . "</p>";
            echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($row['mensagem']) . "</p>";

            echo "<div>";
            echo "<button class='btn' onclick=\"processarAcao('aceitar', " . $row['id'] . ", '$cardId')\">Aceitar</button>";
            echo "<button class='btn' onclick=\"processarAcao('negar', " . $row['id'] . ", '$cardId')\">Negar</button>";
            echo "</div>";

            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma justificativa para verificar.</p>";
    }
} else {
    echo "<p>Erro ao consultar justificativas: " . htmlspecialchars($conn->error) . "</p>";
}

$conn->close();
?>

<script>
function processarAcao(acao, id, cardId) {
    if (!confirm('Tem certeza que deseja ' + acao + ' esta justificativa?')) return;

    fetch('verificar.php?acao=' + acao + '&id=' + id)
        .then(response => {
            if (response.ok) {
                // ✅ Remove o card da tela
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
