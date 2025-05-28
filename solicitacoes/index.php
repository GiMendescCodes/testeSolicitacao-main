<?php
$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT id, data_escolhida, opcao, status FROM dados ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);

$sqlFolga = "SELECT data_escolhida FROM dados WHERE opcao = 'Folgas' AND status = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFolga = $conn->query($sqlFolga);
$proxFolga = null;
if ($resultFolga && $resultFolga->num_rows > 0) {
    $rowFolga = $resultFolga->fetch_assoc();
    $proxFolga = $rowFolga['data_escolhida'];
}

$sqlFerias = "SELECT data_escolhida FROM dados WHERE opcao = 'Férias' AND status = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
$resultFerias = $conn->query($sqlFerias);
$proxFerias = null;
if ($resultFerias && $resultFerias->num_rows > 0) {
    $rowFerias = $resultFerias->fetch_assoc();
    $proxFerias = $rowFerias['data_escolhida'];
}

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
    <link rel="icon" href="img/augebit.png" type="image/png">


    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #EEEEFF;
            padding: 20px;
            color: #333;
        }

        .perfil {
            background-image: url(./img/bola.png);
            display: flex;
            align-items: center;
            border-radius: 40px;
            margin-top: 50px;
            width: 65px;
            height: 68px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .person {
            background-image: url(./img/person.png);
            width: 35px;
            height: 28px;
            margin-left: 3px
        }

        .tudo {
            margin-left: 150px;
            padding: 20px;
            width: 1200px;
            /* largura fixa */
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            gap: 15px;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .logo img {
            width: 20px;
            height: 20px;
        }

        .header-text h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 4px;
        }

        .header-text p {
            color: #718096;
            font-size: 16px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 300px 550px 350px;
            /* Se precisar ajuste o tamanho */
            gap: 25px;
            margin-bottom: 30px;
        }

        .count-cards {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .count-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 0;
            /* Remover padding do card */
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: none;
            position: relative;
            width: fit-content;
            overflow: hidden;
            height: 220px;
            width: 315px;
            display: flex;
            align-items: center;
        }

        .count-card .image-placeholder {
            width: 100%;
            height: auto;
        }

        .imgFe img {
            width: 160px;
            height: 160px;
        }

        .imgFo img {
            width: 240px;
            height: 240px;
        }

        .contagemT {
            position: relative;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-left: -150px;
            margin-top: -150px;
            display: flex;
            flex-direction: column;
        }

        .count-number {
            font-size: 64px;
            font-weight: 700;
            color: #5A4AE3;
            line-height: 1;
            align-items: flex-end;
            align-content: end;
            text-align: end;
        }

        .count-text {
            color: #333333;
            font-size: 14px;
            font-family: 'Montserrat Alternates', sans-serif;
            line-height: 1.2;
            font-weight: 500;
            /* margin-top: -15px; */
        }

        .ladoalado {
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            align-content: end;
            text-align: end;
            margin-bottom: -10px;
        }


        .history-section {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            font-family: Arial, sans-serif;
            border: 2px solid #9998FF;
            height: 460px;
            width: 500px;
        }

        .tituloH {
            background-color: #9998FF;
            width: 100%;
            padding: 16px 0;
            text-align: center;
        }

        .tituloH h2 {
            color: #ffffff;
            margin: 0;
            font-size: 1.5rem;
        }

        .history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            /* padding interno só nos itens */
            border-bottom: 1px solid #9998FF;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-date {
            font-weight: bold;
            margin-right: 10px;
            min-width: 50px;
        }

        .history-content {
            flex: 1;
            /* ocupa o espaço disponível */
            text-align: left;
            /* garante alinhamento à esquerda */
        }

        .history-content p {
            margin: 0;
        }

        .status-icon img {
            width: 40px;
            height: 40px;
        }




        .calendar-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            height: 465px;
            width: 415px;
            box-sizing: border-box;
            overflow: hidden;
            margin-left: -50px;
        }

        .calendar-table {
            transform: scale(0.9);
            /* reduz proporcionalmente */
            transform-origin: top center;
            /* define onde a escala ocorre */
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-title {
            font-size: 18px;
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

        .form-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 300px 300px;
            /* tamanhos fixos */
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #4a5568;
            font-size: 14px;
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
            font-size: 14px;
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
            font-size: 14px;
            color: #4a5568;
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
            font-size: 14px;
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
            resize: none;
            /* evita esticar e deformar */
            font-family: inherit;
        }

        .submit-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            align-self: flex-start;
        }

        .submit-btn:hover {
            background: #5a67d8;
        }

        .fc {
            font-size: 14px;
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

        .fixo {
            position: fixed;
            left: 20px;
            top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar {
            width: 70px;
            background-color: #6c63ff;
            height: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 40px;
        }

        .menu-item {
            width: 50px;
            height: 50px;
            margin: 25px 0;
            background: none;
            border: none;
            position: relative;
            cursor: pointer;
            outline: none;
        }

        .menu-item .icon {
            width: 100%;
            height: 100%;
            display: block;
            mask-size: cover;
            -webkit-mask-size: cover;
            background-color: white;
        }

        .icon.home {
            mask: url('./img/home.png') no-repeat center;
        }

        .icon.notebook {
            mask: url('./img/justificativas.png') no-repeat center;
        }

        .icon.cap {
            mask: url('./img/cursos.png') no-repeat center;
        }

        .icon.chart {
            mask: url('./img/desempenho.png') no-repeat center;
        }

        .icon.phone {
            mask: url('./img/solicitacoes.png') no-repeat center;
        }

        .icon-circle {
            width: 55px;
            height: 80px;
            background-color: #4d47c3;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .icon-circle img {
            width: 30px;
            height: 30px;
        }

        .tituloH {
            background-color: #9998FF;
            width: 100%;
            font-size: 20px;
            text-align: center;
        }

        /* Fundo do calendário */
        #calendar {
            background-color: #F3F4FF;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: scale(0.90);
    transform-origin: top center;

    max-width: 800px; /* largura desejada */
    max-height: 400px; /* altura máxima */
    overflow: hidden; /* corta o que passar */

        }

        /* Fundo das células */
        .fc-daygrid-day {
            background-color: #E0E1FF;
            /* Fundo pastel */
            border: 1px solid #9998FF;
            /* Bordas lilás claro */
        }

        /* Texto das datas */
        .fc-daygrid-day-number {
            color: #4746D8;
            /* Azul profundo */
            font-weight: 600;
        }

        /* Eventos */
        .fc-event {
            background-color: #6E6DFF !important;
            /* Azul-violeta */
            color: #FFFFFF !important;
            border-radius: 8px;
            padding: 4px 6px;
            font-size: 12px;
            border: none;
        }

        /* Dia atual */
        .fc-day-today {
            background-color: #39C9E1 !important;
            /* Azul turquesa */
            border-radius: 8px;
        }

        /* Dias fora do mês */
        .fc-day-other {
            background-color: #C7C8FF;
            /* Lilás muito claro */
            color: #9998FF;
            /* Texto lilás suave */
        }

        /* Cabeçalho dos dias da semana */
        .fc-col-header-cell {
            background-color: #F3F4FF;
            color: #4746D8;
            font-weight: 700;
            border: none;
        }

        /* Título */
        .calendar-title {
            font-size: 20px;
            font-weight: 700;
            color: #4746D8;
            /* Azul profundo */
        }

        /* Botões de navegação */
        .nav-btn {
            background: #E0E1FF;
            color: #4746D8;
            border: none;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .nav-btn:hover {
            background: #C7C8FF;
        }
    </style>

</head>

<body>
    <div class="fixo">
        <img class="logo" src="./img/augebit.png" alt="">
        <div class="sidebar">
            <a href="" class="menu-item">
                <span class="icon home"></span>
            </a>
            <a href="notebook.html" class="menu-item">
                <span class="icon notebook"></span>
            </a>
            <a href="cap.html" class="menu-item">
                <span class="icon cap"></span>
            </a>
            <a href="chart.html" class="menu-item">
                <span class="icon chart"></span>
            </a>
            <div class="icon-circle">
                <img src="img/calendario.png" alt="">
            </div>
        </div>
        <div class="perfil">
            <a class="person" href=""></a>
        </div>
    </div>

    <div class="tudo">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="header-text">
                    <h1>Olá, Giovanna!</h1>
                    <p>Acompanhe seus solicitamentos</p>
                </div>
            </div>
        </div>
        <!-- Main Grid Layout -->
        <div class="main-grid">
            <!-- Cards de Contagem -->
            <div class="count-cards">
                <div class="count-card">
                    <div class="image-placeholder">
                        <img class="imgFe" src="img/ferias.png" />
                    </div>
                    <div class="contagemT">
                        <div class="ladoalado">
                            <div class="count-number"><?php echo ($semanasFerias ?? '30'); ?></div>
                            <div class="count-text">semanas</div>
                        </div>
                        <div class="count-text">para as suas férias</div>
                    </div>
                </div>

                <div class="count-card">
                    <div class="image-placeholder">
                        <img class="imgFo" src="img/folga.png" />
                    </div>
                    <div class="contagemT">
                        <div class="ladoalado">
                            <div class="count-number"><?php echo ($semanasFolga ?? '01'); ?></div>
                            <div class="count-text">semana</div>
                        </div>
                        <div class="count-text">para a próxima folga</div>
                    </div>
                </div>
            </div>


            <!-- Histórico -->
            <div class="history-section">
                <div class="tituloH">
                    <h2>Histórico de solicitações</h2>
                </div>
                <div id="historico">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = 'status-' . $row['status'];
                            $statusIcon = match ($row['status']) {
                                'pendente' => '<img src="img/pendente.png" alt="">',
                                'aceito' => '<img src="img/aprovado.png" alt="">',
                                'negado' => '<img src="img/negado.png" alt="">',
                                default => '<img src="img/pendente.png" alt="">',

                            };
                            $date = new DateTime($row['data_escolhida']);
                            $formattedDate = $date->format('d/m');

                            echo "<div class='history-item' data-id='{$row['id']}'>
                                <div class='history-date'>{$formattedDate}</div>
                                <div class='history-content'>
                                    <p>Solicitou <strong>" . htmlspecialchars($row['opcao']) . "</strong> para " . htmlspecialchars($date->format('d \d\e F \d\e Y')) . ".</p>
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
                    <textarea name="mensagem" class="form-input message-input" required autocomplete="off"
                        placeholder="Descreva detalhes da sua solicitação..."></textarea>
                </div>
                <button type="submit" class="submit-btn">ENVIAR</button>
            </form>
        </div>
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

        document.getElementById('solicitacaoForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('processar.php', {
                method: 'POST',
                body: formData
            }).then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        const historico = document.getElementById('historico');
                        const historyItem = document.createElement('div');
                        historyItem.classList.add('history-item');
                        historyItem.dataset.id = data.id;

                        const date = new Date(data.data);
                        const formattedDate = date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });

                        historyItem.innerHTML = `
                <div class='history-date'>${formattedDate}</div>
                <div class='history-content'>
                    <p>Solicitou <strong>${data.opcao}</strong> para ${date.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' })}.</p>
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

        document.addEventListener('DOMContentLoaded', function () {
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
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown')) {
                document.getElementById('dropdown-options').style.display = 'none';
                document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar;

            if (calendarEl) {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'pt-br',
                    initialView: 'dayGridMonth',
                    events: 'eventos.php?',
                    eventDisplay: 'block',
                    headerToolbar: false,
                    dayMaxEvents: false,
                    height: 'auto'
                });
                calendar.render();

                // Atualiza o título ao mudar de mês
                function updateTitle() {
                    const date = calendar.getDate();
                    const formatter = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' });
                    document.querySelector('.calendar-title').innerText = formatter.format(date);
                }

                // Botões personalizados
                document.querySelector('.month-nav .nav-btn:first-child').addEventListener('click', function () {
                    calendar.prev();
                    updateTitle();
                });

                document.querySelector('.month-nav .nav-btn:last-child').addEventListener('click', function () {
                    calendar.next();
                    updateTitle();
                });

                // Inicializa o título
                updateTitle();
            }
        });

    </script>

    </div>
    </div>
</body>

</html>