<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $acao = $_GET['acao'];

    if ($acao == 'aceitar') {
        $novo_status = 'aceito';
    } elseif ($acao == 'negar') {
        $novo_status = 'negado';
    } else {
        $novo_status = 'pendente';
    }

    $sql = "UPDATE dados SET STATUS=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $novo_status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: verificar.php");
    exit;
}

$sql = "SELECT id, data_escolhida, mensagem, opcao, arquivo FROM dados ORDER BY id DESC";
$result = $conn->query($sql);
?>

<style>
.card {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin: 15px auto;
    max-width: 400px;
    background: #fff;
    box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
}
.card button {
    margin-right: 10px;
}
</style>

<h2>Verificação de Justificativas</h2>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars($row['data_escolhida']) . "</p>";
        echo "<p><strong>Opção:</strong> " . htmlspecialchars($row['opcao']) . "</p>";
        echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($row['mensagem']) . "</p>";

        // Botões de ação
        echo "<div>";
        echo "<a href='verificar.php?acao=aceitar&id=" . $row['id'] . "'><button>Aceitar</button></a>";
        echo "<a href='verificar.php?acao=negar&id=" . $row['id'] . "'><button>Negar</button></a>";
        echo "</div>";

        echo "</div>";
    }
} else {
    echo "<p>Nenhuma justificativa para verificar.</p>";
}

$conn->close();
?>
