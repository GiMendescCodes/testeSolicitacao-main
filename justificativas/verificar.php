<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Buscar status para AJAX
if (isset($_GET['acao'], $_GET['id']) && $_GET['acao'] === 'status') {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("SELECT status FROM justificativas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($status);
    if ($stmt->fetch()) {
        echo json_encode(['status' => $status]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Justificativa não encontrada']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// Upload do arquivo enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $mensagem = $_POST['mensagem'] ?? null;
    $dataEscolhida = $_POST['data_escolhida'] ?? date('Y-m-d');
    $opcao = $_POST['opcao'] ?? '';

    $arquivo = $_FILES['arquivo'];

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        $nomeArquivo = basename($arquivo['name']);
        $novoCaminho = $uploadDir . uniqid() . "_" . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $novoCaminho)) {
            $stmt = $conn->prepare("INSERT INTO justificativas (data_escolhida, opcao, arquivo, mensagem, status) VALUES (?, ?, ?, ?, 'pendente')");
            $stmt->bind_param('ssss', $dataEscolhida, $opcao, $novoCaminho, $mensagem);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    // Redireciona para a mesma página ou outra de confirmação
    header("Location: verificar.php?sucesso=1");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: verificar.php?erro=1");
    exit;
}


            $stmt->close();
        } else {
            echo "Erro ao mover o arquivo enviado.";
        }
    } else {
        echo "Erro no upload do arquivo.";
    }

    $conn->close();
    exit;
}

// Atualizar status aceitar/negar via GET
if (isset($_GET['acao'], $_GET['id'])) {
    $acao = $_GET['acao'];
    $id = (int) $_GET['id'];

    if ($acao === 'aceitar' || $acao === 'negar') {
        $novoStatus = ($acao === 'aceitar') ? 'aceito' : 'negado';

        $stmt = $conn->prepare("UPDATE justificativas SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $novoStatus, $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo "Status atualizado para $novoStatus";
        } else {
            http_response_code(500);
            echo "Erro ao atualizar status";
        }
        $stmt->close();
    } else if ($acao !== 'status') {
        http_response_code(400);
        echo "Ação inválida";
    }

    $conn->close();
    exit;
}

// Listar justificativas
$sql = "SELECT id, data_escolhida, opcao, data_envio, arquivo, mensagem, status FROM justificativas ORDER BY id DESC";
$result = $conn->query($sql);

echo '<h2>Verificação de Justificativas</h2>';

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $cardId = "card_" . $row['id'];
            echo "<div class='card' id='$cardId'>";
            echo "<p><strong>Data Escolhida:</strong> " . htmlspecialchars($row['data_escolhida']) . "</p>";
            echo "<p><strong>Opção:</strong> " . htmlspecialchars($row['opcao']) . "</p>";
            echo "<p><strong>Data de Envio:</strong> " . htmlspecialchars($row['data_envio']) . "</p>";
            echo "<p><strong>Mensagem:</strong> " . nl2br(htmlspecialchars($row['mensagem'])) . "</p>";
            
            $arquivoUrl = htmlspecialchars($row['arquivo']);
            echo "<p><strong>Arquivo:</strong> <a href='$arquivoUrl' target='_blank'>Download</a></p>";

            echo "<p><strong>Status:</strong> <span class='status-text' data-id='" . $row['id'] . "'>" . htmlspecialchars($row['status']) . "</span></p>";

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
                atualizarStatusDoCard(id);
            } else {
                alert('Erro ao processar a ação.');
            }
        })
        .catch(error => {
            alert('Erro de conexão.');
            console.error(error);
        });
}
function atualizarStatusDoCard(id) {
    fetch('verificar.php?acao=status&id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                const spanStatus = document.querySelector('.status-text[data-id="' + id + '"]');
                if (spanStatus) {
                    spanStatus.textContent = data.status;
                }
            }
        });
}
function atualizarTodosStatus() {
    const spans = document.querySelectorAll('.status-text');
    spans.forEach(span => {
        const id = span.getAttribute('data-id');
        atualizarStatusDoCard(id);
    });
}

setInterval(atualizarTodosStatus, 5000);
</script>
