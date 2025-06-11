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
                header("Location: verificar.php?sucesso=1");
                exit;
            } else {
                $stmt->close();
                $conn->close();
                header("Location: verificar.php?erro=1");
                exit;
            }
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

// Contar justificativas pendentes
$countSql = "SELECT COUNT(*) as total FROM justificativas WHERE status = 'pendente'";
$countResult = $conn->query($countSql);
$pendingCount = $countResult ? $countResult->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Justificativas</title>
    <link rel="icon" href="img/augebit.png" type="image/png">
    <style>
        @font-face {
            font-family: 'fonte1';
            src: url('../fontes/euroStyle Normal.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fonte2';
            src: url('../fontes/Montserrat-VariableFont_wght.ttf');
        }

        .tituloInicial {
            margin-bottom: 30px;
            margin-top: 15px;
        }

        .titulo {
            font-family: 'fonte1', euroStyle;
            font-weight: normal;
            font-style: normal;
            font-size: 50px;
        }

        .subtitulo {
            font-family: 'fonte2', Montserrat, sans-serif;
            font-weight: 300;
            font-size: 25px;
        }

        .tudo {
            margin-left: 200px;
            width: 1200px;
            display: flex;
            flex-direction: row;
        }

        .fixo {
            position: fixed;
            left: 20px;
            top: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar {
            width: 70px;
            background-color: #6c63ff;
            height: 480px;
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
            width: 50px;
            height: 50px;
            display: block;
            mask-size: cover;
            -webkit-mask-size: cover;
            background-color: white;
        }

        .icon.home {
            mask: url('./img/home.png') no-repeat center;
        }

        .icon.people {
            mask: url('./img/ph_person-thin.png') no-repeat center;
        }

        .icon.docs {
            mask: url('./img/docs.png') no-repeat center;
        }

        .icon.chapeu {
            mask: url('./img/chapeu.png') no-repeat center;
        }

        .icon.grafico {
            mask: url('./img/grafico.png') no-repeat center;
        }

        .icon.calendario {
            mask: url('./img/calendario.png') no-repeat center;
        }

        .icon-circle {
            width: 55px;
            height: 60px;
            background-color: #4d47c3;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .icon-circle img {
            width: 30px;
            height: 30px;
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            height: 1000px;
            padding: 20px;
            background-color: #EEEEFF;
        }

        .carousel-section {
            margin-bottom: 40px;
        }

        .carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-container {
            overflow: hidden;
            width: 710px;
            border-radius: 20px;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -webkit-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -moz-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .carousel-card {
            height: 255px;
            width: 710px;
            background: white;
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: center;
            gap: 50px;
            flex-shrink: 0;
        }

        .card-illustration img {
            width: 205px;
            height: 210px;
        }

        .card-content {
            width: 710px;
        }

        .card-title {
            font-size: 20px;
            color: #9998FF;
            margin-bottom: 8px;
            font-family: 'fonte2', sans-serif;
            font-weight: 600;
            margin-top: 20px;
        }

        .card-subtitle {
            font-size: 15px;
            color: black;
            margin-bottom: 20px;
            font-family: 'fonte2', sans-serif;
            margin-left: 50px;
            font-weight: bold;
            margin-top: -5px;
        }

        .card-text {
            font-size: 13px;
            color: black;
            line-height: 1.6;
            margin-bottom: 25px;
            font-family: 'fonte2', sans-serif;
            font-style: normal;
            font-weight: normal;
        }

        .card-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            background-color: white;
            padding: 10px 16px;
            margin-top: -15px;
            border-radius: 0 0 30px 30px;
            height: auto;
            position: absolute;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e0e0e0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: #667eea;
            transform: scale(1.2);
        }

        .main-panel {
            width: 1140px;
        }

        .parte1 {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .status-summary {
            background: white;
            border-radius: 30px;
            padding: 20px;
            width: 400px;
            height: 250px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            margin-left: 45px;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .status-left {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 10px;
            max-width: 50%;
            margin-top: -120px;
        }

        .status-number {
            font-size: 20px;
            color: white;
            background-color: #9998FF;
            height: 28px;
            width: 38px;
            border-radius: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
            font-weight: lighter;
        }

        .status-label {
            font-family: 'fonte2', sans-serif;
            color: black;
            font-size: 20px;
            line-height: 1.2;
            z-index: 2;
        }

        .analise {
            margin-left: -40px;
        }

        .pendentes {
            background: none;
            display: inline;
            box-shadow: inset 0 -14px rgba(153, 152, 255, 0.5);
            padding: 0;
            margin-left: -40px;
        }

        .status-summary img {
            width: 280px;
            height: 250px;
            padding-right: 50px;
            z-index: 1;
        }

        .solicitation-card {
            background: none;
            border: 5px solid #6E6DFF;
            border-radius: 60px;
            padding: 30px;
            margin-bottom: 20px;
            position: relative;
            width: 1170px;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -webkit-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -moz-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .divRoxo {
            background-color: #9998FF;
            height: 22px;
            width: 15px;
            position: relative;
            margin-left: -20px;
            margin-right: -20px;
        }

        .employee-avatar {
            width: 50px;
            height: 50px;
            background: #9998FF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }

        .employee-name {
            font-size: 15px;
            color: white;
            background-color: #9998FF;
            border-radius: 30px;
            font-family: 'fonte2', sans-serif;
            font-style: normal;
            width: 230px;
            height: 35px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 15px;
        }

        .request-type,
        .request-date {
            padding: 6px 12px;
            border-radius: 20px;
        }

        .request-type {
            background: #9998FF;
            color: white;
            font-family: 'fonte2', sans-serif;
            font-style: normal;
        }

        .request-date {
            background-color: #9998FF;
            color: white;
            font-family: 'fonte2', sans-serif;
            font-style: normal;
        }

        .card-content-text {
            background: #9998FF;
            border-radius: 30px;
            padding: 20px;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.6;
            color: white;
            font-family: 'fonte2', sans-serif;
            font-style: normal;
        }

        .card-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-approve {
            background: #6E6DFF;
            color: white;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -webkit-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -moz-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .btn-deny {
            background: #6E6DFF;
            color: white;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -webkit-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -moz-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .btn-approve:hover {
            background-color: white;
            color: #9998FF;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -webkit-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -moz-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .btn-deny:hover {
            background-color: white;
            color: #9998FF;
            box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -webkit-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
            -moz-box-shadow: 2px 5px 19px -1px rgba(153, 152, 255, 0.7);
        }

        .no-requests {
            text-align: center;
            padding: 60px;
            color: #666;
            font-size: 18px;
        }

        .download-link {
            color: #9998FF;
            text-decoration: none;
            font-weight: 600;
        }

        .download-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="fixo">
        <img class="logo" src="./img/augebit.png" alt="">
        <div class="sidebar">
            <a href="" class="menu-item"><span class="icon home"></span></a>
            <a href="" class="menu-item"><span class="icon people"></span></a>
            <a href="" class="menu-item"><span class="icon docs"></span></a>
            <a href="" class="menu-item"><span class="icon chapeu"></span></a>
            <a href="" class="menu-item"><span class="icon grafico"></span></a>
            <a href="" class="icon-circle"> <img src="img/calendarioBranco.png" alt=""> </a>
        </div>
    </div>

    <div class="tudo">
        <div class="content-area">
            <div class="tituloInicial">
                <h1 class="titulo">Painel de justificativas</h1>
                <h3 class="subtitulo">Analise as justificativas feitas pelos funcionários</h3>
            </div>
            <div class="container">
                <div class="parte1">
                    <div class="carousel-section">
                        <div class="carousel-container">
                            <div class="carousel-wrapper" id="carousel">
                                <!-- Card 1 -->
                                <div class="carousel-card">
                                    <div class="card-illustration">
                                        <img src="img/card1.png" alt="">
                                    </div>
                                    <div class="card-content">
                                        <div class="card-title">Comunicado Importante!</div>
                                        <div class="card-subtitle">Atualização sobre o novo modelo de férias.</div>
                                        <div class="card-text">
                                            Atenção! As regras para solicitação de férias foram atualizadas. A partir
                                            deste mês, todos os pedidos devem ser realizados com, no mínimo, 30 dias de
                                            antecedência. Além disso, está disponível a opção de divisão das férias em
                                            até três períodos, sendo que nenhum deles pode ser inferior a 5 dias.
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2 -->
                                <div class="carousel-card">
                                    <div class="card-illustration">
                                        <img src="img/card2.png" alt="">
                                    </div>
                                    <div class="card-content">
                                        <div class="card-title">Aniversariantes do Mês</div>
                                        <div class="card-subtitle">Comemoração dos Aniversariantes de Junho</div>
                                        <div class="card-text">
                                            Neste mês, celebramos o aniversário de nossos colaboradores. Desejamos a todos muitas
                                            felicidades, sucesso e realizações! Que este novo ciclo seja repleto de
                                            conquistas e momentos especiais. Parabéns a todos!
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3 -->
                                <div class="carousel-card">
                                    <div class="card-illustration">
                                        <img src="img/card3.png" alt="">
                                    </div>
                                    <div class="card-content">
                                        <div class="card-title">Meta do Mês</div>
                                        <div class="card-subtitle">Meta de Aprovação de Justificativas</div>
                                        <div class="card-text">
                                            Nosso objetivo para este mês é alcançar 95% de aprovações de justificativas
                                            dentro do prazo estabelecido. Para isso, reforçamos a importância de revisar os
                                            pedidos com agilidade e precisão, garantindo que nossa análise seja feita dentro do
                                            prazo de até 48 horas após o recebimento.
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 4 -->
                                <div class="carousel-card">
                                    <div class="card-illustration">
                                        <img src="img/card4.png" alt="">
                                    </div>
                                    <div class="card-content">
                                        <div class="card-title">Treinamento Programado</div>
                                        <div class="card-subtitle">Workshop de Desenvolvimento Profissional</div>
                                        <div class="card-text">
                                            Participe do nosso Workshop de Desenvolvimento Profissional, que acontecerá
                                            no dia 20 de maio, das 14h às 17h, na sala de conferências. Os temas abordados
                                            serão: liderança, gestão de tempo, comunicação eficaz e planejamento
                                            estratégico.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-dots">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>

                    <div class="status-summary">
                        <div class="status-left">
                            <div class="status-number">
                                <?php echo sprintf('%02d', $pendingCount); ?>
                            </div>
                            <div class="status-label">
                                justificativas<br><span class="pendentes">pendentes</span> de <span class="analise">análise</span>
                            </div>
                        </div>
                        <img src="img/numero.png" alt="">
                    </div>
                </div>

                <!-- Painel Principal -->
                <div class="main-panel">
                    <div class="panel-content">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $cardId = "card_" . $row['id'];
                                
                                // Criar nome fictício baseado no ID ou usar apenas "Nome Sobrenome"
                                $nomes = ["João Silva", "Maria Santos", "Pedro Costa", "Ana Oliveira", "Carlos Souza", "Luciana Ferreira"];
                                $nomeIndex = $row['id'] % count($nomes);
                                $nome = $nomes[$nomeIndex];
                                
                                // Criar iniciais para o avatar
                                $iniciais = strtoupper(substr($nome, 0, 1) . substr(strrchr($nome, ' '), 1, 1));

                                echo "<div class='solicitation-card' id='$cardId'>";
                                echo "<div class='card-header'>";
                                echo "<div class='employee-avatar'>$iniciais</div>";
                                echo "<div class='divRoxo'></div>";
                                echo "<div class='employee-info'>";
                                echo "<div class='employee-name'>$nome</div>";
                                echo "</div>";
                                echo "<div class='request-type'>" . htmlspecialchars($row['opcao'] ?? 'Justificativa') . "</div>";
                                echo "<div class='request-date'>" . date('d/m/Y', strtotime($row['data_escolhida'])) . "</div>";
                                echo "</div>";

                                echo "<div class='card-content-text'>";
                                echo htmlspecialchars($row['mensagem'] ?? 'Lorem ipsum dolor sit amet consectetur. Proin consequat elit enim vitae. Ut mauris auctor mauris posuere amet morbi tempus volutpat. Nullam rhoncus ornare id nisl. A facilisis arcu eget augue. Lorem ipsum dolor sit amet consectetur. Proin consequat elit enim vitae. Ut mauris auctor mauris posuere amet morbi tempus volutpat. Nullam rhoncus ornare id nisl. A facilisis arcu eget augue Lorem ipsum dolor sit amet consectetur. Proin consequat elit enim vitae. Ut mauris auctor mauris posuere amet morbi tempus volutpat.');
                                
                                if (!empty($row['arquivo'])) {
                                    echo "<br><br><strong>Arquivo anexado:</strong> ";
                                    echo "<a href='" . htmlspecialchars($row['arquivo']) . "' target='_blank' class='download-link'>DOCUMENTO(PDF)</a>";
                                }
                                echo "</div>";

                                if ($row['status'] === 'pendente') {
                                    echo "<div class='card-actions'>";
                                    echo "<button class='btn btn-approve' onclick=\"processarAcao('aceitar', " . $row['id'] . ", '$cardId')\">Aprovar justificativa</button>";
                                    echo "<button class='btn btn-deny' onclick=\"processarAcao('negar', " . $row['id'] . ", '$cardId')\">Negar justificativa</button>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='card-actions'>";
                                    echo "<div style='padding: 10px 20px; background: #FFFFFF; color: #9998FF; border-radius: 20px; font-weight: 600;'>";
                                    echo "Status: " . ucfirst($row['status']);
                                    echo "</div>";
                                    echo "</div>";
                                }

                                echo "</div>";
                            }
                        } else {
                            echo "<div class='no-requests'>";
                            echo "<p>📭 Nenhuma justificativa para verificar.</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = 4;

        function updateCarousel() {
            const slideWidth = 710;
            const carousel = document.getElementById('carousel');

            const offset = currentSlide * slideWidth;
            carousel.style.transform = `translateX(-${offset}px)`;

            document.querySelectorAll('.dot').forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function previousSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        // Auto-advance
        setInterval(nextSlide, 5000);

        // Make dots clickable
        document.querySelectorAll('.dot').forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateCarousel();
            });
        });

        function processarAcao(acao, id, cardId) {
            if (!confirm('Tem certeza que deseja ' + acao + ' esta justificativa?')) return;

            fetch('?acao=' + acao + '&id=' + id)
                .then(response => {
                    if (response.ok) {
                        location.reload();
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
            fetch('?acao=status&id=' + id)
                .then(res => res.json())
                .then(data => {
                    