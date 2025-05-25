<form action="processar.php" method="post" enctype="multipart/form-data">
    <label>Data:</label>
    <input type="date" name="data" required><br>

    <label>Mensagem:</label>
    <input type="text" name="mensagem" required><br>

    <label>Opção:</label>
    <div class="dropdown" onclick="toggleDropdown()">
        <div class="dropdown-btn" id="selected-option">Escolha uma opção ▼</div>
        <div id="dropdown-options" style="display: none;">
            <div onclick="selecionarOpcao('Motivo pessoal')">Motivo pessoal</div>
            <div onclick="selecionarOpcao('Emergência familiar')">Emergência familiar</div>
            <div onclick="selecionarOpcao('Consulta médica (com atestado)')">Consulta médica (com atestado)</div>
            <div onclick="selecionarOpcao('Consulta médica (sem atestado)')">Consulta médica (sem atestado)</div>
            <div onclick="selecionarOpcao('Outro')">Outro</div>
        </div>
    </div>
    <input type="hidden" name="opcao" id="opcao-selecionada" required>

    <label>Anexar Arquivo:</label>
    <input type="file" name="arquivo" required><br>

    <button type="submit">Enviar</button>
</form>

<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT data_escolhida, opcao, STATUS FROM justificativas ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);

if ($result === false) {
    die("Erro na consulta SQL: " . $conn->error);
}
?>

<h2>Histórico de Justificativas</h2>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<div>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars($row['data_escolhida']) . "</p>";
        echo "<p><strong>Opção:</strong> " . htmlspecialchars($row['opcao']) . "</p>";
        echo "</div>";

        $status = $row['STATUS'];
        if ($status == 'pendente') {
            $img = 'img/pendente.png';
            $alt = 'Pendente';
        } elseif ($status == 'aceito') {
            $img = 'img/aprovado.png';
            $alt = 'Aprovado';
        } else {
            $img = 'img/negado.png';
            $alt = 'Negado';
        }

        echo "<img src='$img' alt='$alt'>";
        echo "</div>";
    }
} else {
    echo "<p>Nenhuma justificativa encontrada.</p>";
}

$conn->close();
?>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown-options');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function selecionarOpcao(opcao) {
    document.getElementById('opcao-selecionada').value = opcao;
    document.getElementById('selected-option').innerText = opcao + ' ▼';
    document.getElementById('dropdown-options').style.display = 'none';
}

// Opcional: fechar dropdown ao clicar fora
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown-options');
    const dropdownContainer = document.querySelector('.dropdown');
    if (!dropdownContainer.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<style>
.dropdown {
    width: 250px;
    border: 1px solid #ccc;
    padding: 10px;
    position: relative;
    cursor: pointer;
    user-select: none;
    border-radius: 5px;
    background: white;
    margin-bottom: 10px;
}

.dropdown-btn {
    font-weight: bold;
}

#dropdown-options div {
    padding: 10px;
    background: #f9f9f9;
    cursor: pointer;
}

#dropdown-options div:hover {
    background: #e0e0e0;
}

.card {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin: 15px auto;
    max-width: 400px;
    background: #fff;
    box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card img {
    width: 50px;
    height: 50px;
}
</style>
