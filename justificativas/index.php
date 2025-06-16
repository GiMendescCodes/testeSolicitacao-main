<?php
$conn = new mysqli("localhost", "root", "", "solicitacaoo");
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_escolhida = $_POST['data_escolhida'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    $opcao = $_POST['opcao'] ?? '';
    $arquivo = ''; // default para campo obrigatório NOT NULL

    if (!$data_escolhida || !$mensagem || !$opcao) {
        $mensagemErro = "Preencha todos os campos obrigatórios.";
    } else {
        // Verifica se arquivo foi enviado
        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
            $arquivo_tmp = $_FILES['arquivo']['tmp_name'];
            $arquivo_nome = basename($_FILES['arquivo']['name']);
            $destino = 'uploads/' . uniqid() . '_' . $arquivo_nome;

            if (!move_uploaded_file($arquivo_tmp, $destino)) {
                $mensagemErro = "Erro ao salvar o arquivo.";
            } else {
                $arquivo = $destino; // caminho do arquivo salvo
            }
        }

        if (!$mensagemErro) {
            $stmt = $conn->prepare("INSERT INTO justificativas (data_escolhida, mensagem, opcao, status, arquivo) VALUES (?, ?, ?, 'pendente', ?)");
            $stmt->bind_param("ssss", $data_escolhida, $mensagem, $opcao, $arquivo);

            if (!$stmt->execute()) {
                $mensagemErro = "Erro ao salvar justificativa: " . $stmt->error;
            } else {
                // Redireciona para evitar duplicação no refresh
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $stmt->close();
        }
    }
}

$sql = "SELECT id, data_escolhida, opcao, mensagem, status, arquivo FROM justificativas ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificativas</title>
    <style>
        @font-face {
            font-family: 'fonte1';
            src: url('../fontes/eurostile.TTF');
        }

        @font-face {
            font-family: 'fonte2';
            src: url('../fontes/Montserrat-VariableFont_wght.ttf');
        }

        @font-face {
            font-family: 'fonte3';
            src: url('../fontes/MontserratAlternates-Regular.ttf');
        }

        .fixo {
            position: fixed;
            left: 20px;
            top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
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

        .sidebar {
            width: 70px;
            background-color: #6c63ff;
            height: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 40px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #EEEEFF;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 30px;
            height: calc(100vh - 40px);
        }


        .nav-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-icon:hover,
        .nav-icon.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            overflow: hidden;
        }

        .left-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .header {
            color: white;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: start;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: lighter;
            margin-bottom: 5px;
            font-family: 'fonte1', sans-serif;
            color: black;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-family: 'fonte2' sans-serif;
            color: black;
            margin-top: -15px;
        }

        .history-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            flex: 1;
            overflow: hidden;
        }

        .history-title {
            color: #667eea;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .history-section {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(136, 92, 255, 0.3);
            overflow: hidden;
            font-family: Arial, sans-serif;
            border: 2px solid #9998FF;
            height: 368px;
            width: 515px;
        }

        .tituloH {
            background-color: #9998FF;
            width: 100%;
            padding: 16px 0;
            text-align: center;
            font-family: 'fonte3', sans-serif;
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
            font-family: 'fonte2', sans-serif;
            border-bottom: 1px solid #9998FF;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-date {
            font-weight: bold;
            margin-right: 10px;
            min-width: 50px;
            font-family: 'fonte2', sans-serif;
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

        .status-icon {

            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .status-icon img {
            width: 45px;
            height: 45px;
        }

        .right-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .quote-card {
            display: flex;
            flex-direction: row;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 60px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(136, 92, 255, 0.3);
            position: relative;
            overflow: hidden;
            width: 630px;
            height: 365px;
            margin-left: 15px;
        }

        .quote-illustration {
            width: 295px;
            height: 295px;
            border-radius: 15px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }


        .quote-text {
            color: #9998FF;
            line-height: 1.6;
            font-size: 20px;
            width: 242px;
            font-family: 'fonte3', sans-serif;
        }

        .form-card {
            background: white;
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 30px;
            flex: 1;
            box-shadow: 0 0 10px rgba(136, 92, 255, 0.3);
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .form-group {
            flex: 1;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #9998FF;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            outline: none;
            border-color: #9998FF;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
            color: #667eea;
            opacity: 0.7;
        }

        .dropdown {
            position: relative;
            width: 100%;
        }

        .dropdown-btn {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #9998FF;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #9998FF;
        }

        .dropdown-btn:hover {
            border-color: #9998FF;
            background: white;
        }

        .dropdown-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            margin-top: 5px;
            overflow: hidden;
        }

        .dropdown-option {
            padding: 15px 20px;
            cursor: pointer;
            transition: background 0.2s ease;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .dropdown-option:last-child {
            border-bottom: none;
        }

        .dropdown-option:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            border: 2px solid #9998FF;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #9998FF;
            background: rgba(255, 255, 255, 0.5);
        }

        .file-input-label:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .textarea {
            min-height: 120px;
            resize: vertical;
            padding-top: 15px;
        }

        .submit-btn {
            background: #9998FF;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            align-self: flex-end;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .error {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(244, 67, 54, 0.2);
        }

        @media (max-width: 1200px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .form-row {
                flex-direction: column;
            }
        }

        .ladoalado {
            display: flex;
            flex-direction: row;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="fixo">
            <img class="logo" src="./img/augebit.png" alt="">
            <div class="sidebar">
                <a href="" class="menu-item">
                    <span class="icon home"></span>
                </a>
                <div class="icon-circle">
                    <img src="img/papelbranco.png" alt="">
                </div>
                <a href="cap.html" class="menu-item">
                    <span class="icon cap"></span>
                </a>
                <a href="chart.html" class="menu-item">
                    <span class="icon chart"></span>
                </a>
                <a href="phone.html" class="menu-item">
                    <span class="icon phone"></span>
                </a>
            </div>
            <div class="perfil">
                <a class="person" href=""></a>
            </div>
        </div>
        <div class="tudo">
            <div class="main-content">
                <div class="left-section">
                    <div class="header">
                        <h1>Olá, Giovanna!</h1>
                        <p>Acompanhe suas justificativas</p>
                    </div>
                    <div class="ladoalado">
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
                                                echo "<p>Justificou uma falta/atraso por <strong>$opcao</strong></p>";
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

                        <div class="right-section">
                            <div class="quote-card">
                                <div class="quote-illustration"><img src="img/quote.png" alt=""></div>
                                <p class="quote-text">
                                    A verdadeira excelência está em reconhecer nossos erros, corrigi-los com
                                    determinação e
                                    seguir em frente com ainda mais força. Cada presença conta para o nosso sucesso
                                    coletivo!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <?php if ($mensagemErro): ?>
                            <div class="error"><?php echo htmlspecialchars($mensagemErro); ?></div>
                        <?php endif; ?>

                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group">
                                    <input type="date" name="data_escolhida" class="form-input"
                                        placeholder="Data da falta" required>
                                </div>

                                <div class="form-group">
                                    <div class="dropdown" onclick="toggleDropdown()">
                                        <div class="dropdown-btn" id="selected-option">
                                            <span>Selecione a opção que melhor descreve sua justificativa</span>
                                            <span>→</span>
                                        </div>
                                        <div class="dropdown-options" id="dropdown-options" style="display: none;">
                                            <div class="dropdown-option" onclick="selecionarOpcao('Motivo pessoal')">
                                                Motivo pessoal</div>
                                            <div class="dropdown-option"
                                                onclick="selecionarOpcao('Emergência familiar')">Emergência familiar
                                            </div>
                                            <div class="dropdown-option"
                                                onclick="selecionarOpcao('Consulta médica (com atestado)')">Consulta
                                                médica (com atestado)</div>
                                            <div class="dropdown-option"
                                                onclick="selecionarOpcao('Consulta médica (sem atestado)')">Consulta
                                                médica (sem atestado)</div>
                                            <div class="dropdown-option" onclick="selecionarOpcao('Outro')">Outro
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="opcao" id="opcao-selecionada" required>
                                </div>

                                <div class="form-group">
                                    <div class="file-input-wrapper">
                                        <input type="file" name="arquivo" id="arquivo" class="file-input">
                                        <label for="arquivo" class="file-input-label">
                                            <span>📎</span>
                                            <span>Anexe um arquivo, se necessário</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <textarea name="mensagem" class="form-input textarea"
                                    placeholder="Explique sua solicitação" required></textarea>
                            </div>

                            <button type="submit" class="submit-btn">ENVIAR</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown-options');
            const expanded = dropdown.style.display === 'block';
            dropdown.style.display = expanded ? 'none' : 'block';
        }

        function selecionarOpcao(opcao) {
            document.getElementById('selected-option').innerHTML = `<span>${opcao}</span><span>→</span>`;
            document.getElementById('opcao-selecionada').value = opcao;
            document.getElementById('dropdown-options').style.display = 'none';
        }

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function (event) {
            const dropdown = document.querySelector('.dropdown');
            if (!dropdown.contains(event.target)) {
                document.getElementById('dropdown-options').style.display = 'none';
            }
        });

        // Atualizar label do arquivo quando selecionado
        document.getElementById('arquivo').addEventListener('change', function (e) {
            const label = document.querySelector('.file-input-label span:last-child');
            if (e.target.files.length > 0) {
                label.textContent = e.target.files[0].name;
            } else {
                label.textContent = 'Anexe um arquivo, se necessário';
            }
        });
    </script>
</body>

</html>