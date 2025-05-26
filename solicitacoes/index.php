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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solicitações</title>
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales-all.global.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f5f7fa;
        padding: 20px;
        color: #333;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header */
    .header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        gap: 15px;
    }

    .logo {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .header-text h1 {
        font-size: 2rem;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 4px;
    }

    .header-text p {
        color: #718096;
        font-size: 1rem;
    }

    /* Main Grid Layout */
    .main-grid {
        display: grid;
        grid-template-columns: 300px 1fr 350px;
        gap: 25px;
        margin-bottom: 30px;
    }

    /* Cards de Contagem */
    .count-cards {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .count-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .count-card .image-placeholder {
        width: 120px;
        height: 80px;
        background: #f7fafc;
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        color: #a0aec0;
        font-size: 12px;
    }

    .count-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
    }

    .count-text {
        color: #4a5568;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    /* Histórico */
    .history-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .history-section h2 {
        color: #2d3748;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .history-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-date {
        background: #667eea;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        min-width: 50px;
        text-align: center;
    }

    .history-content {
        flex: 1;
    }

    .history-content p {
        color: #4a5568;
        font-size: 0.85rem;
        line-height: 1.4;
        margin: 0;
    }

    .status-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: white;
        flex-shrink: 0;
    }

    /* Calendário */
    .calendar-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .calendar-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }

    .month-nav {
        display: flex;
        gap: 8px;
    }

    .nav-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: #f7fafc;
        border-radius: 6px;
        color: #4a5568;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-btn:hover {
        background: #edf2f7;
    }

    /* Formulário */
    .form-section {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        color: #4a5568;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-input {
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: border-color 0.2s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .dropdown {
        position: relative;
    }

    .dropdown-btn {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        color: #4a5568;
    }

    .dropdown-btn:hover {
        border-color: #667eea;
    }

    .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        margin-top: 4px;
    }

    .dropdown-options div {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 0.9rem;
        color: #4a5568;
    }

    .dropdown-options div:hover {
        background: #f7fafc;
    }

    .message-area {
        grid-column: 1 / -1;
    }

    .message-input {
        min-height: 100px;
        resize: vertical;
        font-family: inherit;
    }

    .submit-btn {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease;
        align-self: flex-start;
    }

    .submit-btn:hover {
        background: #5a67d8;
    }

    /* FullCalendar customizations */
    .fc {
        font-size: 0.85rem;
    }

    .fc-header-toolbar {
        display: none;
    }

    .fc-daygrid-day-number {
        color: #4a5568;
        font-weight: 500;
    }

    .fc-day-today {
        background: rgba(102, 126, 234, 0.1) !important;
    }

    .fc-daygrid-day:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .main-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .count-cards {
            flex-direction: row;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .count-cards {
            flex-direction: column;
        }
    }
</style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="logo">📋</div>
        <div class="header-text">
            <h1>Olá, Giovanna!</h1>
            <p>Acompanhe seus solicitamentos</p>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="main-grid">
        <!-- Cards de Contagem -->
        <div class="count-cards">
            <div class="count-card">
                <div class="image-placeholder">
                    <img src="img/ferias.png" />
                </div>
                <div class="count-number"><?php echo ($semanasFerias ?? '30'); ?></div>
                <div class="count-text">semanas para as suas férias</div>
            </div>
            
            <div class="count-card">
                <div class="image-placeholder">
                    <img src="img/folga.png" />
                </div>
                <div class="count-number"><?php echo ($semanasFolga ?? '01'); ?></div>
                <div class="count-text">semana para a próxima folga</div>
            </div>
        </div>

        <!-- Histórico -->
        <div class="history-section">
            <div class="Thist">
            <h2>Histórico de solicitações</h2>
            </div>
            <div id="historico">
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $statusClass = 'status-' . $row['status'];
                        $statusIcon = match($row['status']) {
                        'pendente' => '<img src="img/pendente.png" alt="">',
                        'aceito' => '<img src="img/aprovado.png" alt="">',
                        'negado' => '✗ <img src="img/negado.png" alt="">',
                        default => '● <img src="img/pendente.png" alt="">',

                        };
                        $date = new DateTime($row['data_escolhida']);
                        $formattedDate = $date->format('d/m');
                        
                        echo "<div class='history-item' data-id='{$row['id']}'>
                                <div class='history-date'>{$formattedDate}</div>
                                <div class='history-content'>
                                    <p>Solicitou <strong>".htmlspecialchars($row['opcao'])."</strong> para ".htmlspecialchars($date->format('d \d\e F \d\e Y')).".</p>
                                </div>
                                <div class='status-icon {$statusClass}'>{$statusIcon}</div>
                              </div>";
                    }
                } else {
                    echo "<p style='text-align: center; color: #a0aec0; font-size: 0.9rem;'>Você ainda não fez nenhuma solicitação.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Calendário -->
        <div class="calendar-section">
            <div class="calendar-header">
                <div class="calendar-title">Junho 2025</div>
                <div class="month-nav">
                    <button class="nav-btn">←</button>
                    <button class="nav-btn">→</button>
                </div>
            </div>
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="form-section">
        <form id="solicitacaoForm">
            <div class="form-row">
                <div class="form-group">
                    <label>📅 Data que você deseja marcar a solicitação</label>
                    <input type="date" name="data" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>🌐 Selecione a opção que melhor descreve sua solicitação</label>
                    <div class="dropdown" onclick="toggleDropdown()" role="button" aria-expanded="false">
                        <div class="dropdown-btn" id="selected-option">
                            <span>Escolha uma opção</span>
                            <span>▼</span>
                        </div>
                        <div id="dropdown-options" class="dropdown-options" style="display: none;">
                            <div onclick="selecionarOpcao('Home office')">Home office</div>
                            <div onclick="selecionarOpcao('Treinamento')">Treinamento</div>
                            <div onclick="selecionarOpcao('Férias')">Férias</div>
                            <div onclick="selecionarOpcao('Folgas')">Folgas</div>
                        </div>
                    </div>
                    <input type="hidden" name="opcao" id="opcao-selecionada" required>
                </div>
            </div>
            <div class="form-group message-area">
                <label>✏️ Explique sua solicitação</label>
                <textarea name="mensagem" class="form-input message-input" required autocomplete="off" placeholder="Descreva detalhes da sua solicitação..."></textarea>
            </div>
            <button type="submit" class="submit-btn">ENVIAR</button>
        </form>
    </div>
</div>

<script>
function toggleDropdown() {
    let dropdown = document.getElementById('dropdown-options');
    let isOpen = dropdown.style.display === 'block';
    dropdown.style.display = isOpen ? 'none' : 'block';
    document.querySelector('.dropdown').setAttribute('aria-expanded', !isOpen);
}

function selecionarOpcao(opcao) {
    document.querySelector('#selected-option span:first-child').innerText = opcao;
    document.getElementById('opcao-selecionada').value = opcao;
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
            const historyItem = document.createElement('div');
            historyItem.classList.add('history-item');
            historyItem.dataset.id = data.id;

            const date = new Date(data.data);
            const formattedDate = date.toLocaleDateString('pt-BR', {day: '2-digit', month: '2-digit'});

            historyItem.innerHTML = `
                <div class='history-date'>${formattedDate}</div>
                <div class='history-content'>
                    <p>Solicitou <strong>${data.opcao}</strong> para ${date.toLocaleDateString('pt-BR', {day: 'numeric', month: 'long', year: 'numeric'})}.</p>
                </div>
                <div class='status-icon status-pendente'>●</div>
            `;
            historico.prepend(historyItem);

            this.reset();
            document.querySelector('#selected-option span:first-child').innerText = 'Escolha uma opção';
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
            eventDisplay: 'block',
            headerToolbar: false,
            dayMaxEvents: false,
            height: 'auto'
        });
        calendar.render();
    }
});

// Fechar dropdown ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.getElementById('dropdown-options').style.display = 'none';
        document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
    }
});
</script>

</body>
</html>