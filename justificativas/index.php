<?php
// Conexão com banco
$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$mensagemErro = '';

// --- Envia justificativa ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_escolhida'])) {
    $data_escolhida = $_POST['data_escolhida'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    $opcao = $_POST['opcao'] ?? '';

    if (!$data_escolhida || !$mensagem || !$opcao) {
        $mensagemErro = "Preencha todos os campos obrigatórios.";
    } else {
        $stmt = $conn->prepare("INSERT INTO justificativas (data_escolhida, mensagem, opcao, status) VALUES (?, ?, ?, 'pendente')");
        $stmt->bind_param("sss", $data_escolhida, $mensagem, $opcao);
        if (!$stmt->execute()) {
            $mensagemErro = "Erro ao salvar justificativa: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Busca histórico ---
$sql = "SELECT id, data_escolhida, opcao, mensagem, status FROM justificativas ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<title>Justificativas</title>
<style>
.dropdown {
    position: relative;
    width: 250px;
    cursor: pointer;
    user-select: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px;
    background: #fff;
}
.dropdown-btn { font-size: 14px; }
#dropdown-options {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    width: 100%;
    max-height: 150px;
    overflow-y: auto;
    z-index: 1000;
}
#dropdown-options div { padding: 6px; }
#dropdown-options div:hover { background: #eee; }
.card {
    border:1px solid #ccc;
    border-radius:10px;
    padding:10px;
    margin:10px auto;
    max-width:400px;
    background: #fff;
}
.card img { width:40px; height:40px; }
.info-box {
    margin: 10px auto;
    max-width: 400px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}
.error { color: red; margin-bottom: 10px; }
</style>
</head>
<body>

<h1>Enviar Justificativa</h1>

<?php if($mensagemErro): ?>
    <p class="error"><?php echo htmlspecialchars($mensagemErro); ?></p>
<?php endif; ?>

<form id="justificativaForm" enctype="multipart/form-data" method="post" action="">
    <label for="data_escolhida">Data:</label><br>
    <input type="date" id="data_escolhida" name="data_escolhida" required><br><br>

    <label for="mensagem">Mensagem:</label><br>
    <textarea id="mensagem" name="mensagem" rows="3" cols="30" placeholder="Escreva aqui sua justificativa..." required></textarea><br><br>

    <label>Selecione a opção:</label><br>
    <div class="dropdown" onclick="toggleDropdown()" role="button" aria-expanded="false" tabindex="0" onblur="fecharDropdown()">
        <div class="dropdown-btn" id="selected-option">Escolha uma opção ▼</div>
        <div id="dropdown-options" style="display: none;">
            <div onclick="selecionarOpcao('Motivo pessoal')">Motivo pessoal</div>
            <div onclick="selecionarOpcao('Emergência familiar')">Emergência familiar</div>
            <div onclick="selecionarOpcao('Consulta médica (com atestado)')">Consulta médica (com atestado)</div>
            <div onclick="selecionarOpcao('Consulta médica (sem atestado)')">Consulta médica (sem atestado)</div>
            <div onclick="selecionarOpcao('Outro')">Outro</div>
        </div>
    </div>
    <input type="hidden" name="opcao" id="opcao-selecionada" required><br><br>

    <button type="submit">Enviar Justificativa</button>
</form>

<h2>Histórico de Justificativas</h2>
<div id="historico">
    <?php
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $img = match($row['status']) {
                'pendente' => 'img/pendente.png',
                'aceito' => 'img/aprovado.png',
                'negado' => 'img/negado.png',
                default => 'img/pendente.png'
            };
            $alt = ucfirst($row['status']);

            echo "<div class='card'>
                    <div>
                        <p><strong>Data:</strong> ".htmlspecialchars($row['data_escolhida'])."</p>
                        <p><strong>Opção:</strong> ".htmlspecialchars($row['opcao'])."</p>
                        <p><strong>Mensagem:</strong> ".htmlspecialchars($row['mensagem'])."</p>
                        <p><strong>Status:</strong> ".htmlspecialchars($row['status'])."</p>
                    </div>
                    <img src='{$img}' alt='{$alt}'>
                  </div>";
        }
    } else {
        echo "<p>Você ainda não fez nenhuma solicitação.</p>";
    }
    ?>
</div>

<script>
function toggleDropdown() {
    let dropdown = document.getElementById('dropdown-options');
    let isOpen = dropdown.style.display === 'block';
    dropdown.style.display = isOpen ? 'none' : 'block';
    document.querySelector('.dropdown').setAttribute('aria-expanded', !isOpen);
}

function fecharDropdown() {
    setTimeout(() => {
        document.getElementById('dropdown-options').style.display = 'none';
        document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
    }, 150);
}

function selecionarOpcao(opcao) {
    document.getElementById('opcao-selecionada').value = opcao;
    document.getElementById('selected-option').innerText = opcao + ' ▼';
    document.getElementById('dropdown-options').style.display = 'none';
    document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
}
</script>

</body>
</html>
