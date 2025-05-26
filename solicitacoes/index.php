<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales-all.global.min.js"></script>

<form action="processar.php" method="post">
    <label>Data:</label>
    <input type="date" name="data" required><br>

    <label>Mensagem:</label>
    <input type="text" name="mensagem" required autocomplete="off"><br>

    <label>Opção:</label>
    <div class="dropdown" onclick="toggleDropdown()" role="button" aria-expanded="false">
        <div class="dropdown-btn" id="selected-option">Escolha uma opção ▼</div>
        <div id="dropdown-options" style="display: none;">
            <div onclick="selecionarOpcao('Home office')">Home office</div>
            <div onclick="selecionarOpcao('Treinamento')">Treinamento</div>
            <div onclick="selecionarOpcao('Férias')">Férias</div>
            <div onclick="selecionarOpcao('Folgas')">Folgas</div>
        </div>
    </div>
    <input type="hidden" name="opcao" id="opcao-selecionada">

    <input type="hidden" name="tipo" value="solicitacao">

    <button type="submit">Enviar</button>
</form>

<?php
$conn = new mysqli("localhost", "root", "", "solicitacaoo");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sqlFolga = "SELECT data_escolhida FROM dados WHERE opcao = 'Folgas' AND STATUS = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFolga = $conn->query($sqlFolga);
$proxFolga = null;

if ($resultFolga !== false && $resultFolga->num_rows > 0) {
    $row = $resultFolga->fetch_assoc();
    $proxFolga = $row['data_escolhida'];
}

$sqlFerias = "SELECT data_escolhida FROM dados WHERE opcao = 'Férias' AND STATUS = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFerias = $conn->query($sqlFerias);
$proxFerias = null;

if ($resultFerias !== false && $resultFerias->num_rows > 0) {
    $row = $resultFerias->fetch_assoc();
    $proxFerias = $row['data_escolhida'];
}

$hoje = new DateTime();
$semanasFolga = $proxFolga ? ceil($hoje->diff(new DateTime($proxFolga))->days / 7) : null;
$semanasFerias = $proxFerias ? ceil($hoje->diff(new DateTime($proxFerias))->days / 7) : null;

$sql = "SELECT data_escolhida, opcao, STATUS FROM dados ORDER BY id DESC LIMIT 6";
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

        echo "<img src='" . htmlspecialchars($img) . "' alt='" . htmlspecialchars($alt) . "'>";
        echo "</div>";
    }
} else {
    echo "<p>Você ainda não fez nenhuma solicitação.</p>";
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
    var isOpen = dropdown.style.display === 'block';
    dropdown.style.display = isOpen ? 'none' : 'block';
    document.querySelector('.dropdown').setAttribute('aria-expanded', !isOpen);
}

function selecionarOpcao(opcao) {
    document.getElementById('opcao-selecionada').value = opcao;
    document.getElementById('selected-option').innerText = opcao + ' ▼';
    document.getElementById('dropdown-options').style.display = 'none';
    document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            events: 'eventos.php?',
            eventDisplay: 'block'
        });
        calendar.render();
    }
});
</script>
