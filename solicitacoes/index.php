<?php
$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Pega os últimos 6 registros para histórico
$sql = "SELECT id, data_escolhida, opcao, status FROM dados ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);

// Próxima folga aceita e futura
$sqlFolga = "SELECT data_escolhida FROM dados WHERE opcao = 'Folgas' AND status = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFolga = $conn->query($sqlFolga);
$proxFolga = null;
if ($resultFolga && $resultFolga->num_rows > 0) {
    $rowFolga = $resultFolga->fetch_assoc();
    $proxFolga = $rowFolga['data_escolhida'];
}

// Próxima férias aceita e futura
$sqlFerias = "SELECT data_escolhida FROM dados WHERE opcao = 'Férias' AND status = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFerias = $conn->query($sqlFerias);
$proxFerias = null;
if ($resultFerias && $resultFerias->num_rows > 0) {
    $rowFerias = $resultFerias->fetch_assoc();
    $proxFerias = $rowFerias['data_escolhida'];
}

// Calcula semanas restantes
$hoje = new DateTime();
$semanasFolga = null;
$semanasFerias = null;
if ($proxFolga) {
    $diff = $hoje->diff(new DateTime($proxFolga));
    $semanasFolga = ceil($diff->days / 7);
}
if ($proxFerias) {
    $diff = $hoje->diff(new DateTime($proxFerias));
    $semanasFerias = ceil($diff->days / 7);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<title>Solicitações</title>
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales-all.global.min.js"></script>
<style>
.dropdown { /* seu estilo aqui */ }
.card {
    border:1px solid #ccc;
    border-radius:10px;
    padding:10px;
    margin:10px auto;
    max-width:400px;
    display:flex;
    justify-content: space-between;
    align-items:center;
}
.card img {width:40px; height:40px;}
.info-box {
    margin: 10px auto;
    max-width: 400px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}
</style>
</head>
<body>

<form id="solicitacaoForm">
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
    <input type="hidden" name="opcao" id="opcao-selecionada" required>
    <button type="submit">Enviar</button>
</form>

<h2>Histórico de Solicitações</h2>
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
            echo "<div class='card' data-id='{$row['id']}'>
                    <div>
                        <p><strong>Data:</strong> ".htmlspecialchars($row['data_escolhida'])."</p>
                        <p><strong>Opção:</strong> ".htmlspecialchars($row['opcao'])."</p>
                    </div>
                    <img src='{$img}' alt='{$alt}'>
                  </div>";
        }
    } else {
        echo "<p>Você ainda não fez nenhuma solicitação.</p>";
    }
    ?>
</div>

<!-- Mostrar as semanas restantes para próxima folga e férias -->
<div class="info-box">
    <?php if ($semanasFolga !== null): ?>
        <p>Faltam <strong><?php echo $semanasFolga; ?></strong> semana(s) para a próxima <strong>Folga</strong>.</p>
    <?php else: ?>
        <p>Sem <strong>Folgas</strong> agendadas.</p>
    <?php endif; ?>
</div>

<div class="info-box">
    <?php if ($semanasFerias !== null): ?>
        <p>Faltam <strong><?php echo $semanasFerias; ?></strong> semana(s) para as próximas <strong>Férias</strong>.</p>
    <?php else: ?>
        <p>Sem <strong>Férias</strong> agendadas.</p>
    <?php endif; ?>
</div>

<h2>Calendário de Solicitações</h2>
<div id='calendar'></div>

<script>
function toggleDropdown() {
    let dropdown = document.getElementById('dropdown-options');
    let isOpen = dropdown.style.display === 'block';
    dropdown.style.display = isOpen ? 'none' : 'block';
    document.querySelector('.dropdown').setAttribute('aria-expanded', !isOpen);
}

function selecionarOpcao(opcao) {
    document.getElementById('opcao-selecionada').value = opcao;
    document.getElementById('selected-option').innerText = opcao + ' ▼';
    document.getElementById('dropdown-options').style.display = 'none';
    document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
}

document.getElementById('solicitacaoForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('processar.php', {
        method: 'POST',
        body: formData
    }).then(resp => resp.json())
    .then(data => {
        if(data.success) {
            const historico = document.getElementById('historico');
            const card = document.createElement('div');
            card.classList.add('card');
            card.dataset.id = data.id;

            card.innerHTML = `
                <div>
                    <p><strong>Data:</strong> ${data.data}</p>
                    <p><strong>Opção:</strong> ${data.opcao}</p>
                </div>
                <img src="img/pendente.png" alt="Pendente">
            `;
            historico.prepend(card);

            this.reset();
            document.getElementById('selected-option').innerText = 'Escolha uma opção ▼';
        } else {
            alert(data.message || 'Erro ao enviar solicitação');
        }
    }).catch(() => alert('Erro na comunicação com o servidor.'));
});

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

</body>
</html>
