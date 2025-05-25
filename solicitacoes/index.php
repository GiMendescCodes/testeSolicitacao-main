<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales-all.global.min.js"></script>

<form action="processar.php" method="post">
    <label>Data:</label>
    <input type="date" name="data" required><br>

    <label>Mensagem:</label>
    <input type="text" name="mensagem" required><br>

    <label>Opção:</label>
    <div class="dropdown" onclick="toggleDropdown()">
        <div class="dropdown-btn" id="selected-option">Escolha uma opção ▼</div>
        <div id="dropdown-options" style="display: none;">
            <div onclick="selecionarOpcao('Home office')">Home office</div>
            <div onclick="selecionarOpcao('Treinamento')">Treinamento</div>
            <div onclick="selecionarOpcao('Férias')">Férias</div>
            <div onclick="selecionarOpcao('Folgas')">Folgas</div>
        </div>
    </div>
    <input type="hidden" name="opcao" id="opcao-selecionada">

    <input type="hidden" name="tipo" value="solicitacao"> <!-- Novo: para identificar no processar -->

    <button type="submit">Enviar</button>
</form>

<?php
$conn = new mysqli("localhost", "root", "", "solicitacao");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Pega próxima folga do tipo solicitacao
$sqlFolga = "SELECT data_escolhida FROM dados WHERE opcao = 'Folgas' AND STATUS = 'aceito' AND tipo = 'solicitacao' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFolga = $conn->query($sqlFolga);
$proxFolga = null;

if ($resultFolga->num_rows > 0) {
    $row = $resultFolga->fetch_assoc();
    $proxFolga = $row['data_escolhida'];
}

// Pega próxima férias do tipo solicitacao
$sqlFerias = "SELECT data_escolhida FROM dados WHERE opcao = 'Férias' AND STATUS = 'aceito' AND tipo = 'solicitacao' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFerias = $conn->query($sqlFerias);
$proxFerias = null;

if ($resultFerias->num_rows > 0) {
    $row = $resultFerias->fetch_assoc();
    $proxFerias = $row['data_escolhida'];
}

// Calcula semanas
$hoje = new DateTime();
$semanasFolga = $proxFolga ? ceil($hoje->diff(new DateTime($proxFolga))->days / 7) : null;
$semanasFerias = $proxFerias ? ceil($hoje->diff(new DateTime($proxFerias))->days / 7) : null;

// Busca últimas 6 solicitações do tipo solicitacao
$sql = "SELECT data_escolhida, opcao, STATUS FROM dados WHERE tipo = 'solicitacao' ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);

if ($result === false) {
    die("Erro na consulta SQL: " . $conn->error);
}
?>

<h2>Histórico de Solicitações</h2>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<div>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars($row['data_escolhida']) . "</p>";
        echo "<p><strong>Opção:</strong> " . htmlspecialchars($row['opcao']) . "</p>";
        echo "</div>";

        $status = $row['STATUS'];
        if($status == 'pendente'){
            $img = 'img/pendente.png';
            $alt = 'Pendente';
        } elseif($status == 'aceito'){
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
    echo "<p>Nenhuma solicitação encontrada.</p>";
}

$conn->close();
?>

<h2>Calendário de Solicitações</h2>
<div id='calendar'></div>

<div class="info-box">
    <?php if($semanasFolga !== null): ?>
        <p>Faltam <strong><?php echo $semanasFolga; ?></strong> semana(s) para a próxima <strong>Folga</strong>.</p>
    <?php else: ?>
        <p>Sem <strong>Folgas</strong> agendadas.</p>
    <?php endif; ?>
</div>

<div class="info-box">
    <?php if($semanasFerias !== null): ?>
        <p>Faltam <strong><?php echo $semanasFerias; ?></strong> semana(s) para as próximas <strong>Férias</strong>.</p>
    <?php else: ?>
        <p>Sem <strong>Férias</strong> agendadas.</p>
    <?php endif; ?>
</div>

<script>
function toggleDropdown() {
    var dropdown = document.getElementById('dropdown-options');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function selecionarOpcao(opcao) {
    document.getElementById('opcao-selecionada').value = opcao;
    document.getElementById('selected-option').innerText = opcao + ' ▼';
    document.getElementById('dropdown-options').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        events: 'eventos.php?tipo=solicitacao',  // Passa o tipo no GET para filtrar os eventos
        eventDisplay: 'block'
    });

    calendar.render();
});
</script>

<style>
#calendar {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 10px;
    height: 600px; /* importante para o calendário aparecer */
}

.dropdown {
    width: 200px;
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

.info-box {
    border: 2px solid #007BFF;
    border-radius: 8px;
    padding: 15px;
    margin: 15px auto;
    max-width: 400px;
    background: #f0f8ff;
    box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
    font-size: 16px;
}
</style>
