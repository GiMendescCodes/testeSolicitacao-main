<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['acao'], $_GET['id'])) {
    $acao = $_GET['acao'];
    $id = (int) $_GET['id'];

    if ($acao === 'aceitar' || $acao === 'negar') {
        $novoStatus = ($acao === 'aceitar') ? 'aceito' : 'negado';

        $stmt = $conn->prepare("UPDATE dados SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $novoStatus, $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo "Status atualizado para $novoStatus";
        } else {
            http_response_code(500);
            echo "Erro ao atualizar status";
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo "Ação inválida";
    }

    $conn->close();
    exit;
}

$sql = "SELECT id, data_escolhida, mensagem, opcao, status FROM dados ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Solicitações</title>
    <style>
        .tudo {
            margin-left: 150px;
            padding: 20px;
            width: 1200px;
            /* largura fixa */
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

        /* Exemplo de ícones usando mask (ou pode usar SVG inline) */
        .icon.home {
            mask: url('./img/home.png') no-repeat center;
        }

        .icon.people {
            mask: url('./img/peoplee.png') no-repeat center;
        }

        .icon.docs {
            mask: url('./img/docs.png') no-repeat center;
        }

        .icon.chapeu {
            mask: url('./img/chapeuu.png') no-repeat center;
        }

        .icon.grafico {
            mask: url('./img/grafico.png') no-repeat center;
        }

        .icon.calendario {
            mask: url('./img/calendario.png') no-repeat center;
        }

        .icon.mapa {
            mask: url('./img/mapa.png') no-repeat center;
        }

        /* Estado ativo (com "bolinha") */

        .icon-circle {
            width: 55px;
            height: 60px;
            background-color: #4d47c3;
            /* roxo escuro */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
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
        .icon-circle img {
            width: 30px;
            height: 30px;
        }

        .tudo {
            display: flex;
            flex-direction: row;

        }

        .perfil-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }


        .perfil {
            background-image: url(./img/bola.png);
            display: flex;
            align-items: center;
            margin-left: 40px;
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }


        /* Carrossel de Cards */
        .carousel-section {
            margin-bottom: 40px;
        }

        .carousel-container {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
        }

        .carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease;
            gap: 30px;
        }

        .carousel-card {
            min-width: 100%;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .card-illustration {
            flex: 0 0 300px;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 600;
        }

        .card-content {
            flex: 1;
        }

        .card-title {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
        }

        .card-subtitle {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .card-text {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .card-dots {
            display: flex;
            gap: 8px;
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

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .carousel-nav:hover {
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .carousel-nav.prev {
            left: 20px;
        }

        .carousel-nav.next {
            right: 20px;
        }

        /* Painel de Solicitações */
        .main-panel {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .panel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .panel-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .panel-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .panel-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 80px;
            height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 0;
            gap: 30px;
            z-index: 1000;
        }

        .sidebar-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .content-area {
            margin-left: 100px;
        }

        .panel-content {
            padding: 40px;
        }

        .status-summary {
            position: absolute;
            top: 30px;
            right: 40px;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 200px;
        }

        .status-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .status-label {
            font-size: 14px;
            color: #666;
        }

        /* Cards de Solicitação */
        .solicitation-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #e6f0ff 100%);
            border: 2px solid #667eea;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
            position: relative;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .employee-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .employee-info {
            flex: 1;
        }

        .employee-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .request-type {
            background: #667eea;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .request-date {
            background: #e6f0ff;
            color: #667eea;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-content-text {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.6;
            color: #555;
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
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
        }

        .btn-deny {
            background: linear-gradient(135deg, #f44336 0%, #da190b 100%);
            color: white;
        }

        .btn-deny:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(244, 67, 54, 0.3);
        }

        .no-requests {
            text-align: center;
            padding: 60px;
            color: #666;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .content-area {
                margin-left: 0;
            }

            .carousel-card {
                flex-direction: column;
                text-align: center;
            }

            .card-illustration {
                flex: none;
                width: 100%;
                max-width: 300px;
            }
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
            <a href="" class="Home icon"><span class="icon heat"></span></a>
            <a href="" class="menu-item"><span class="icon grafico"></span></a>
            <div class="icon-circle"><span class="img/calendario.png"></span></div>
        </div>
        <div class="perfil">
            <a class="person" href=""></a>
        </div>
    </div>
    </div>
    <div class="tudo">
        <div class="content-area">
            <div class="container">
                <!-- Carrossel de Cards -->
                <div class="carousel-section">
                    <div class="carousel-container">
                        <div class="carousel-wrapper" id="carousel">
                            <!-- Card 1 -->
                            <div class="carousel-card">
                                <div class="card-illustration">
                                    [Espaço para Imagem 1]
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Comunicado Importante!</div>
                                    <div class="card-subtitle">Atualização sobre o novo modelo de férias.</div>
                                    <div class="card-text">
                                        Atenção! As regras para solicitação de férias foram atualizadas. A partir deste
                                        mês, todos os pedidos devem ser realizados com, no mínimo, 30 dias de
                                        antecedência. Além disso, está disponível a opção de divisão das férias em até
                                        três períodos, sendo que nenhum deles pode ser inferior a 5 dias. Para mais
                                        informações, consulte o manual do colaborador ou entre em contato com o setor de
                                        RH.
                                    </div>
                                    <div class="card-dots">
                                        <div class="dot active"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="carousel-card">
                                <div class="card-illustration">
                                    [Espaço para Imagem 2]
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Aniversariantes do Mês</div>
                                    <div class="card-subtitle">Comemoração dos Aniversariantes de Junho</div>
                                    <div class="card-text">
                                        Neste mês, celebramos o aniversário de nossos colaboradores: João Silva (05/06),
                                        Ana Costa (12/06) e Marcos Pereira (25/06). Desejamos a todos muitas
                                        felicidades, sucesso e realizações! Que este novo ciclo seja repleto de
                                        conquistas e momentos especiais. Parabéns a todos!
                                    </div>
                                    <div class="card-dots">
                                        <div class="dot"></div>
                                        <div class="dot active"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="carousel-card">
                                <div class="card-illustration">
                                    [Espaço para Imagem 3]
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Meta do Mês</div>
                                    <div class="card-subtitle">Meta de Aprovação de Solicitações</div>
                                    <div class="card-text">
                                        Nosso objetivo para este mês é alcançar 95% de aprovações de solicitações dentro
                                        do prazo estabelecido. Para isso, reforçamos a importância de revisar os pedidos
                                        com agilidade e precisão, garantindo que nossa análise seja feita dentro do
                                        prazo de até 48 horas após o recebimento. Contamos com o apoio e a dedicação de
                                        toda a equipe para atingir essa meta e melhorar ainda mais nossos processos
                                        internos!
                                    </div>
                                    <div class="card-dots">
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot active"></div>
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4 -->
                            <div class="carousel-card">
                                <div class="card-illustration">
                                    [Espaço para Imagem 4]
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Treinamento Programado</div>
                                    <div class="card-subtitle">Workshop de Desenvolvimento Profissional</div>
                                    <div class="card-text">
                                        Participe do nosso Workshop de Desenvolvimento Profissional, que acontecerá no
                                        dia 20 de maio, das 14h às 17h, na sala de conferências. Os temas abordados
                                        serão: liderança, gestão de tempo, comunicação eficaz e planejamento
                                        estratégico. As inscrições podem ser realizadas diretamente no portal interno
                                        até o dia 18 de maio. Não perca essa oportunidade de aprimorar suas habilidades
                                        e expandir seus conhecimentos!
                                    </div>
                                    <div class="card-dots">
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot active"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="carousel-nav prev" onclick="previousSlide()">‹</button>
                        <button class="carousel-nav next" onclick="nextSlide()">›</button>
                    </div>
                </div>

                <!-- Painel Principal -->
                <div class="main-panel">
                    <div class="panel-header">
                        <div class="panel-icon">📋</div>
                        <div>
                            <div class="panel-title">Painel de solicitações</div>
                            <div class="panel-subtitle">Analise as solicitações feitas pelos funcionários</div>
                        </div>

                        <?php
                        $countSql = "SELECT COUNT(*) as total FROM dados WHERE status = 'pendente'";
                        $countResult = $conn->query($countSql);
                        $pendingCount = $countResult ? $countResult->fetch_assoc()['total'] : 0;
                        ?>

                        <div class="status-summary">
                            <div class="status-number"><?php echo $pendingCount; ?></div>
                            <div class="status-label">solicitações<br>pendentes de<br>análise</div>
                            <div style="margin-top: 15px;">📊📈🔍</div>
                        </div>
                    </div>

                    <div class="panel-content">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $cardId = "card_" . $row['id'];
                                $initials = strtoupper(substr($row['opcao'] ?? 'NS', 0, 2));

                                echo "<div class='solicitation-card' id='$cardId'>";
                                echo "<div class='card-header'>";
                                echo "<div class='employee-avatar'>$initials</div>";
                                echo "<div class='employee-info'>";
                                echo "<div class='employee-name'>Nome Sobrenome</div>";
                                echo "</div>";
                                echo "<div class='request-type'>" . htmlspecialchars($row['opcao'] ?? '') . "</div>";
                                echo "<div class='request-date'>" . htmlspecialchars($row['data_escolhida'] ?? '') . "</div>";
                                echo "</div>";

                                echo "<div class='card-content-text'>";
                                echo htmlspecialchars($row['mensagem'] ?? 'Lorem ipsum dolor sit amet consectetur. Proin consequat elit enim vitae. Ut mauris auctor mauris posuere amet morbi tempus volutpat. Nullam rhoncus ornare id nisl. A facilisis arcu eget augue. Lorem ipsum dolor sit amet consectetur. Proin consequat elit enim vitae. Ut mauris auctor mauris posuere amet morbi tempus volutpat. Nullam rhoncus ornare id nisl. A facilisis arcu eget augue Lorem ipsum dolor sit amet consectetur. Proin consequat elit enim vitae. Ut mauris auctor mauris posuere amet morbi tempus volutpat.');
                                echo "</div>";

                                if ($row['status'] === 'pendente') {
                                    echo "<div class='card-actions'>";
                                    echo "<button class='btn btn-approve' onclick=\"processarAcao('aceitar', " . $row['id'] . ", '$cardId')\">Aprovar solicitação</button>";
                                    echo "<button class='btn btn-deny' onclick=\"processarAcao('negar', " . $row['id'] . ", '$cardId')\">Negar solicitação</button>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='card-actions'>";
                                    echo "<div style='padding: 10px 20px; background: " . ($row['status'] === 'aceito' ? '#4CAF50' : '#f44336') . "; color: white; border-radius: 20px; font-weight: 600;'>";
                                    echo "Status: " . ucfirst($row['status']);
                                    echo "</div>";
                                    echo "</div>";
                                }

                                echo "</div>";
                            }
                        } else {
                            echo "<div class='no-requests'>";
                            echo "<p>📭 Nenhuma solicitação para verificar.</p>";
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
            const carousel = document.getElementById('carousel');
            carousel.style.transform = `translateX(-${currentSlide * 100}%)`;

            // Update dots
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

        // Auto-advance carousel every 5 seconds
        setInterval(nextSlide, 5000);

        // Make dots clickable
        document.querySelectorAll('.dot').forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateCarousel();
            });
        });

        function processarAcao(acao, id, cardId) {
            if (!confirm('Tem certeza que deseja ' + acao + ' esta solicitação?')) return;

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
    </script>
</body>

</html>

<?php $conn->close(); ?>