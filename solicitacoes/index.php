<?php
$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT id, data_escolhida, opcao, status FROM dados ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);

$sqlFolga = "SELECT data_escolhida FROM dados WHERE opcao = '' AND status = 'aceito' AND data_escolhida >= CURDATE() ORDER BY data_escolhida ASC LIMIT 1";
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
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            -webkit-appearance: none;
            display: none;
        }

        /* Firefox */
        input[type="date"]::-moz-calendar-picker-indicator {
            display: none;
        }

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
            margin-left: -180px;
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
            margin-bottom: -5px;
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


        /* Garantia que largura inclui bordas e padding */
        * {
            box-sizing: border-box;
        }

        .form-section {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(136, 92, 255, 0.3);
            border: 2px solid rgba(136, 92, 255, 0.3);
            width: 1260px;
        }

        .opcao {
            margin-left: -220px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group label {
            color: #b0a8d1;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Ajuste: apenas para inputs e dropdowns */
        .form-input,
        .dropdown-btn {
            padding: 12px 14px;
            border: 1px solid rgba(136, 92, 255, 0.2);
            border-radius: 12px;
            font-size: 13px;
            background: #ffffff;
            color: #a3a3c2;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-input {
            width: 384px;
        }

        .form-input img {
            margin: 5px;
        }

        /* Ajuste: message-area ocupa largura total */
        .message-area {
            width: 100%;
        }

        /* Correção: textarea com largura total e sem conflito */
        .message-input {
            width: 100%;
            min-height: 150px;
            padding: 12px 14px;
            border: 1px solid rgba(136, 92, 255, 0.2);
            border-radius: 12px;
            font-size: 13px;
            background: #ffffff;
            color: #a3a3c2;
            transition: border-color 0.3s, box-shadow 0.3s;
            resize: none;
            /* Evita distorção */
        }

        .form-input:focus,
        .dropdown-btn:focus,
        .message-input:focus {
            outline: none;
            border-color: rgba(136, 92, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(136, 92, 255, 0.2);
        }


        .dropdown {
            position: relative;
        }

        .dropdown-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .dropdown-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid rgba(136, 92, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(136, 92, 255, 0.2);
            z-index: 1000;
            margin-top: 5px;
        }

        .dropdown-options div {
            padding: 10px 14px;
            cursor: pointer;
            font-size: 13px;
            color: #a3a3c2;
        }

        .dropdown-options div:hover {
            background: rgba(136, 92, 255, 0.05);
        }

        .message-area {
            grid-column: 1 / -1;
        }

        .message-input {
            min-height: 120px;
            resize: none;
            font-family: inherit;
        }

        .submit-btn {
            background: #9998FF;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(136, 92, 255, 0.3);
            transition: background 0.3s, transform 0.2s;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);

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

        .calendar-section {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            height: 450px;
            width: 420px;
            box-sizing: border-box;
            overflow: hidden;
            margin-left: -50px;
        }

        thead[role="presentation"] {
            background: none;
        }

        .calendar-table {
            transform: scale(0.9);
            transform-origin: top center;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .calendar-title {
            font-size: 16px;
            font-weight: 700;
            color: #1C1B6D;
            margin-bottom: 12px;
        }

        .month-nav {
            display: flex;
            gap: 8px;
        }

        .nav-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: transparent;
            color: #1C1B6D;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .nav-btn:hover {
            background: rgba(136, 92, 255, 0.1);
        }

        #calendar,
        #calendar * {
            box-sizing: border-box;
        }

        #calendar {
            background-color: #D2D2FF;
            height: 360px;
            border-radius: 24px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 400px;
            overflow: visible;
            margin-left: -10px;
        }

        .fc-daygrid-day {
            background-color: #D2D2FF;
            border: 1px solid black;
            padding: 4px !important;
            min-width: 28px !important;
            min-height: 28px !important;
        }

        .fc-daygrid-day-number {
            color: black;
            font-weight: 500;
            font-size: 11px;
        }

        .fc-day-today {
            background-color: #1EC1BA !important;
            color: #FFFFFF !important;
        }

        .fc-event {
            background-color: #6E6DFF !important;
            color: black !important;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 9px;
            border: none;
        }

        .fc-day-other {
            background-color: #4746D8;
            color: #7C71F8;
        }

        /* Cabeçalho com os nomes dos dias */
        .fc-col-header-cell {
            background-color: #D2D2FF !important;
            color: black !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            text-align: center !important;
            padding: 8px 0 !important;
            border: none !important;
        }

        .fc-view-harness {
            height: 300px !important;
            max-height: 300px !important;
            overflow: hidden !important;
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
                <img src="img/calendarioBranco.png" alt="">
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
                            ?>

                            <div class="history-item" data-id="<?php echo $row['id']; ?>">
                                <div class="history-date"><?php echo $formattedDate; ?></div>
                                <div class="history-content">
                                    <?php
                                    $opcao = htmlspecialchars($row['opcao'] ?? 'Nenhuma opção');
                                    echo "<p>Solicitou <strong>$opcao</strong></p>";
                                    ?>
                                </div>

                                <div class="status-icon <?php echo $statusClass; ?>">
                                    <?php echo $statusIcon; ?>
                                </div>
                            </div>

                            <?php
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
                    <div class="form-group" style="position: relative;">
                        <input type="date" id="data" name="data" class="form-input" required
                            placeholder="Data que você deseja marcar a solicitação"
                            style="padding-left: 50px; padding-right: 10px;">

                        <img src="img/calendarioRoxo.png" alt="Abrir calendário" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%);
        width: 24px; height: 24px; cursor: pointer;" onclick="focarEForcarData()" />
                    </div>




                    <div class="form-group">
                        <div class="opcao">
                            <div class="dropdown" onclick="toggleDropdown()" role="button" aria-expanded="false">
                                <div class="dropdown-btn" id="selected-option">
                                    <span><img src="img/globo.png" alt=""> Selecione a opção que melhor descreve sua
                                        solicitação</span>
                                    <span>▼</span>
                                </div>
                                <div id="dropdown-options" class="dropdown-options" style="display: none;">
                                    <div onclick="selecionarOpcao('Home office')">Home office</div>
                                    <div onclick="selecionarOpcao('Treinamento')">Treinamento</div>
                                    <div onclick="selecionarOpcao('Férias')">Férias</div>
                                    <div onclick="selecionarOpcao('Folga')">Folga</div>
                                </div>
                            </div>
                            <input type="hidden" name="opcao" id="opcao-selecionada" required>
                        </div>
                    </div>
                </div>
                <div class="form-group message-area">
                    <input type="hidden" name="mensagem" id="mensagemOculta">

                    <div id="mensagem" contenteditable="true" class="form-input message-input"
                        onclick="limparMensagem()" onblur="restaurarMensagem()" data-vazio="true">
                        <img src="img/texto.png" alt="" style="width: 20px; vertical-align: middle;"> Explique sua
                        solicitação
                    </div>

                    <button type="submit" class="submit-btn">ENVIAR</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
        // SUBSTITUIR o conteúdo do campo oculto com a mensagem do contenteditable
        document.querySelector('#solicitacaoForm').addEventListener('submit', function (e) {
            const mensagem = document.getElementById('mensagem').innerText.trim();
            document.getElementById('mensagemOculta').value = mensagem;
        });

        // Limpa a mensagem inicial ao clicar na div
        function limparMensagem() {
            const msgDiv = document.getElementById('mensagem');
            if (msgDiv.innerText.includes("Explique sua solicitação")) {
                msgDiv.innerHTML = ""; // Remove texto padrão
            }
        }

        const mensagemDiv = document.getElementById('mensagem');

        document.querySelector('#solicitacaoForm').addEventListener('submit', function (e) {
            const mensagem = mensagemDiv.innerText.trim();
            document.getElementById('mensagemOculta').value = mensagem;
        });

        function limparMensagem() {
            if (mensagemDiv.getAttribute("data-vazio") === "true") {
                mensagemDiv.innerHTML = "";
                mensagemDiv.setAttribute("data-vazio", "false");
            }
        }

        function restaurarMensagem() {
            if (mensagemDiv.innerText.trim() === "") {
                mensagemDiv.innerHTML = `<img src="img/texto.png" alt="" style="width: 20px; vertical-align: middle;"> Explique sua solicitação`;
                mensagemDiv.setAttribute("data-vazio", "true");
            }
        }

        // Dropdown personalizado
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

        // Fecha dropdown se clicar fora
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown')) {
                document.getElementById('dropdown-options').style.display = 'none';
                document.querySelector('.dropdown').setAttribute('aria-expanded', 'false');
            }
        });

        // Envio do formulário
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
                            Solicitou <strong>"${data.opcao}"</strong>
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

        // CALENDÁRIO
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
            height: 350
        });
        calendar.render();

        // Atualiza o título personalizado
        function updateTitle() {
            const date = calendar.getDate();
            const formatter = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' });
            document.querySelector('.calendar-title').innerText = formatter.format(date);
        }

        // Botões de navegação
        document.querySelector('.month-nav .nav-btn:first-child').addEventListener('click', function () {
            calendar.prev();
            updateTitle();
        });

        document.querySelector('.month-nav .nav-btn:last-child').addEventListener('click', function () {
            calendar.next();
            updateTitle();
        });

        updateTitle(); // Inicializa o título
    }
});


        // Sincroniza innerHTML do campo editável com o campo oculto no submit
        const form = document.querySelector('form');
        const editableDiv = document.querySelector('#mensagem');
        const hiddenInput = document.querySelector('input[name="mensagem"]');

        form.addEventListener('submit', function (e) {
            hiddenInput.value = editableDiv.innerHTML;
        });
        function focarEForcarData() {
            const inputData = document.getElementById('data');
            inputData.focus();

            // Tenta forçar o calendário abrir
            if (typeof inputData.showPicker === 'function') {
                inputData.showPicker();
            } else {
                // Fallback: abre manualmente um modal ou simplesmente foca no input
                console.log("showPicker não é suportado neste navegador.");
            }
        }

    </script>


    </div>
    </div>
</body>

</html>