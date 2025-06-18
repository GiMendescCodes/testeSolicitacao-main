<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Desempenho Profissional</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #EEEEFF;
            min-height: 100vh;
            padding: 20px;
        }

        .tudo {
            margin-left: 200px;
            width: 1200px;
            display: flex;
            flex-direction: column;
        }

        .menu-item {
            width: 50px;
            height: 50px;
            margin: 20px 0;
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
            mask: url('./img/pessoinha.png') no-repeat center;
        }

        .icon.docs {
            mask: url('./img/docs.png') no-repeat center;
        }

        .icon.chapeu {
            mask: url('./img/cursos.png') no-repeat center;
        }

        .icon.grafico {
            mask: url('./img/desempenho.png') no-repeat center;
        }

        .icon.calendario {
            mask: url('./img/calendario.png') no-repeat center;
        }

        .icon-circle {
            width: 55px;
            height: 55px;
            background-color: #4d47c3;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .icon-circle img {
            width: 25px;
            height: 25px;
            margin: 10px;
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
            height: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 40px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .header-text h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header-text p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .form-section {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }
        .form-group img,
        .form-group img {
            width: 32px;
            height: 32px;
            margin-bottom: -8px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #9998FF;
            border-radius: 22px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .criteria-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .criteria-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .criteria-table th {
            background: #9998FF;
            color: white;
            padding: 20px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
        }

        .criteria-table td {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .criteria-table tr:last-child td {
            border-bottom: none;
        }

        .criteria-table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .criteria-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .weight-input,
        .score-input {
            width: 80px;
            padding: 10px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            text-align: center;
        }

        .obs-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
        }

        .resume-section {
            margin-bottom: 30px;
        }

        .resume-section textarea {
            width: 100%;
            padding: 20px;
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            resize: vertical;
            min-height: 120px;
        }

        .save-btn {
            background: #9998FF;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .evaluations-section {
            margin-top: 60px;
        }

        .evaluations-section h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .evaluation-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #f0f0f0;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .employee-name {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
        }

        .evaluation-date {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }

        .card-content {
            display: flex;
            gap: 40px;
        }

        .left-content {
            flex: 2;
        }

        .right-content {
            flex: 1;
        }

        .performance-table {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .performance-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .performance-table th {
            background: #9998FF;
            color: white;
            padding: 15px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
        }

        .performance-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .performance-table tr:last-child td {
            border-bottom: none;
        }

        .score-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .score-progress {
            width: 100px;
            height: 8px;
            background: #e0e6ed;
            border-radius: 4px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .score-fill.yellow {
            background: #FED54D;
        }

        .score-fill.blue {
            background: #6E6DFF;
        }

        .score-fill.green {
            background: #55D49D;
        }

        .score-value {
            font-size: 12px;
            font-weight: 600;
            color: #2c3e50;
        }

        .total-score {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 25px;
        }

        .total-score h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .total-score .score {
            font-size: 36px;
            font-weight: 700;
        }

        .chart-container {
            background: #9998FF;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;

        }

        .chart-title {
            text-align: center;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .chart {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 8px;
            height: 120px;
            margin-bottom: 10px;
        }

        .chart-bar {
            width: 20px;
            border-radius: 4px 4px 0 0;
            transition: all 0.3s ease;
        }

        .chart-bar:hover {
            transform: translateY(-2px);
        }

        .chart-labels {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .chart-label {
            width: 20px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }

        .summary-section {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 20px;
        }

        .summary-section h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-section h4::before {
            content: '📊';
            font-size: 18px;
        }

        .performance-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .performance-badge.excellent {
            background: linear-gradient(45deg, #58d68d, #2ecc71);
            color: white;
        }

        .performance-badge.good {
            background: linear-gradient(45deg, #5dade2, #3498db);
            color: white;
        }

        .performance-badge.average {
            background: linear-gradient(45deg, #f7dc6f, #f39c12);
            color: white;
        }

        .motivation-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(136, 92, 255, 0.3);
        }

        .motivation-card::before {
            font-size: 40px;
            position: absolute;
            top: 15px;
            right: 15px;
            opacity: 0.3;
        }

        .motivation-text {
            color: #6E6DFF;
            font-size: 14px;
            line-height: 1.6;
            font-style: italic;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .evaluation-card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="fixo">
        <img class="logo" src="\testeSolicitacao-main\desempenho\img\logo.png" alt="">
        <div class="sidebar">
            <a href="" class="menu-item"><span class="icon home"></span></a>
            <a href="" class="menu-item"><span class="icon people"></span></a>
            <a href="" class="menu-item"><span class="icon docs"></span></a>
            <a href="" class="menu-item"><span class="icon chapeu"></span></a>
            <a href="" class="icon-circle"><img src="img/desempIcon.png" alt=""></a>
            <a href="" class="menu-item"> <span class="icon calendario"></span> </a>
        </div>
    </div>
    <div class="tudo">
        <div class="header">
            <div class="header-text">
                <h1>Painel de Desempenho Profissional</h1>
                <p>Preencha abaixo os dados da avaliação de desempenho do funcionário</p>
            </div>
        </div>

        <div class="form-section">
            <form id="evaluationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label><img src="\testeSolicitacao-main\desempenho\img\personRoxo.png" alt="">Digite o nome do funcionário aqui...</label>
                        <input type="text" id="employeeName" placeholder="Nome do funcionário" required>
                    </div>
                    <div class="form-group">
                        <label><img src="\testeSolicitacao-main\desempenho\img\calendarioRoxo.png" alt="">Digite o mês de avaliação aqui...</label>
                        <input type="month" id="evaluationMonth" required>
                    </div>
                </div>

                <div class="criteria-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Critério</th>
                                <th>Peso (1-5)</th>
                                <th>Nota (0-10)</th>
                                <th>Peso x Nota</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="criteria-name">Pontualidade e assiduidade</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="pontualidade" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="pontualidade" required></td>
                                <td class="result-cell" data-criteria="pontualidade">0</td>
                                <td><input type="text" class="obs-input" data-criteria="pontualidade"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr>
                                <td class="criteria-name">Cumprimento de prazos</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="prazos" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="prazos" required></td>
                                <td class="result-cell" data-criteria="prazos">0</td>
                                <td><input type="text" class="obs-input" data-criteria="prazos"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr>
                                <td class="criteria-name">Qualidade do trabalho entregue</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="qualidade" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="qualidade" required></td>
                                <td class="result-cell" data-criteria="qualidade">0</td>
                                <td><input type="text" class="obs-input" data-criteria="qualidade"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr>
                                <td class="criteria-name">Trabalho em equipe</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="equipe" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="equipe" required></td>
                                <td class="result-cell" data-criteria="equipe">0</td>
                                <td><input type="text" class="obs-input" data-criteria="equipe"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr>
                                <td class="criteria-name">Comunicação e clareza</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="comunicacao" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="comunicacao" required></td>
                                <td class="result-cell" data-criteria="comunicacao">0</td>
                                <td><input type="text" class="obs-input" data-criteria="comunicacao"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr>
                                <td class="criteria-name">Proatividade</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="proatividade" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="proatividade" required></td>
                                <td class="result-cell" data-criteria="proatividade">0</td>
                                <td><input type="text" class="obs-input" data-criteria="proatividade"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr>
                                <td class="criteria-name">Capacidade de resolver problemas</td>
                                <td><input type="number" class="weight-input" min="1" max="5" step="1"
                                        data-criteria="problemas" required></td>
                                <td><input type="number" class="score-input" min="0" max="10" step="0.1"
                                        data-criteria="problemas" required></td>
                                <td class="result-cell" data-criteria="problemas">0</td>
                                <td><input type="text" class="obs-input" data-criteria="problemas"
                                        placeholder="Comente o desempenho neste critério."></td>
                            </tr>
                            <tr style="background: rgba(102, 126, 234, 0.1); font-weight: bold;">
                                <td>Total</td>
                                <td id="totalWeight">0</td>
                                <td></td>
                                <td id="totalResult">0</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="resume-section">
                    <label><img src="resumoIcon" alt=""> Resumo de Avaliação</label>
                    <textarea id="evaluationSummary" placeholder="Digite o resumo da avaliação aqui..."></textarea>
                </div>

                <button type="submit" class="save-btn">SALVAR</button>
            </form>
        </div>

        <div class="evaluations-section">
            <h2>Avaliações Registradas</h2>
            <div id="evaluationsList"></div>
        </div>
    </div>

    <script>
        let evaluations = [];

        function getScoreColor(score) {
            if (score <= 5) return 'yellow';
            if (score <= 8) return 'blue';
            return 'green';
        }

        function getPerformanceLevel(score) {
            if (score >= 8.5) return { level: 'Desempenho excelente', class: 'excellent' };
            if (score >= 7) return { level: 'Desempenho bom', class: 'good' };
            return { level: 'Desempenho médio', class: 'average' };
        }

        function updateCalculations() {
            const criteria = ['pontualidade', 'prazos', 'qualidade', 'equipe', 'comunicacao', 'proatividade', 'problemas'];
            let totalWeight = 0;
            let totalResult = 0;

            criteria.forEach(criterion => {
                const weight = parseFloat(document.querySelector(`input[data-criteria="${criterion}"].weight-input`).value) || 0;
                const score = parseFloat(document.querySelector(`input[data-criteria="${criterion}"].score-input`).value) || 0;
                const result = weight * score;

                document.querySelector(`td[data-criteria="${criterion}"]`).textContent = result.toFixed(1);
                totalWeight += weight;
                totalResult += result;
            });

            document.getElementById('totalWeight').textContent = totalWeight;
            document.getElementById('totalResult').textContent = totalResult.toFixed(1);
        }

        function createChart(data) {
            const maxScore = Math.max(...data.map(d => d.score));
            return data.map(d => {
                const height = (d.score / 10) * 100;
                const color = getScoreColor(d.score);
                return `
                    <div class="chart-bar score-fill ${color}" 
                         style="height: ${height}px;" 
                         title="${d.month}: ${d.score}"></div>
                `;
            }).join('');
        }

        function createChartLabels(data) {
            return data.map(d => `<div class="chart-label">${d.month}</div>`).join('');
        }

        function renderEvaluations() {
            const container = document.getElementById('evaluationsList');

            if (evaluations.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #7f8c8d; font-size: 18px;">Nenhuma avaliação encontrada.</p>';
                return;
            }

            container.innerHTML = evaluations.map((evaluation, index) => {
                const performance = getPerformanceLevel(evaluation.average);
                const chartData = evaluations.slice(Math.max(0, index - 5), index + 1).map((e, i) => ({
                    month: new Date(e.month).toLocaleDateString('pt-BR', { month: 'short' }),
                    score: e.average
                }));

                return `
                    <div class="evaluation-card">
                        <div class="card-header">
                            <div class="employee-name">Desempenho de ${evaluation.name}!</div>
                            <div class="evaluation-date">${new Date(evaluation.month).toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' })}</div>
                        </div>
                        
                        <div class="card-content">
                            <div class="left-content">
                                <h4 style="margin-bottom: 20px; color: #2c3e50;">Seu desempenho em ${new Date(evaluation.month).toLocaleDateString('pt-BR', { month: 'long' })}:</h4>
                                
                                <div class="performance-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Critério</th>
                                                <th>Peso (1-5)</th>
                                                <th>Nota (0-10)</th>
                                                <th>Peso x Nota</th>
                                                <th>Observações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${evaluation.criteria.map(c => `
                                                <tr>
                                                    <td class="criteria-name">${c.name}</td>
                                                    <td>${c.weight}</td>
                                                    <td>
                                                        <div class="score-bar">
                                                            <div class="score-progress">
                                                                <div class="score-fill ${getScoreColor(c.score)}" style="width: ${c.score * 10}%"></div>
                                                            </div>
                                                            <span class="score-value">${c.score}</span>
                                                        </div>
                                                    </td>
                                                    <td>${c.result}</td>
                                                    <td>${c.observation}</td>
                                                </tr>
                                            `).join('')}
                                            <tr style="background: rgba(102, 126, 234, 0.1); font-weight: bold;">
                                                <td>Total</td>
                                                <td>${evaluation.totalWeight}</td>
                                                <td></td>
                                                <td>${evaluation.totalResult}</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h4 style="margin: 30px 0 20px 0; color: #2c3e50;">Seu desempenho nos últimos meses:</h4>
                                
                                <div class="chart-container">
                                    <div class="chart-title">Desempenho Profissional em %</div>
                                    <div class="chart">
                                        ${createChart(chartData)}
                                    </div>
                                    <div class="chart-labels">
                                        ${createChartLabels(chartData)}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="right-content">
                                <div class="summary-section">
                                    <h4>Resumo de Avaliação</h4>
                                    <div class="performance-badge ${performance.class}">
                                        ${performance.level}
                                    </div>
                                    <p style="color: #2c3e50; line-height: 1.6; margin-bottom: 20px;">
                                        ${evaluation.summary}
                                    </p>
                                </div>
                                
                                <div class="motivation-card">
                                    <div class="motivation-text">
                                        Transforme cada desafio em uma oportunidade de crescimento. Isso é o que realmente importa.
                                    </div>
                                    <img src="/testeSolicitacao-main/desempenho/img/ilustration.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Event listeners
        document.addEventListener('input', function (e) {
            if (e.target.matches('.weight-input, .score-input')) {
                updateCalculations();
            }
        });

        document.getElementById('evaluationForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const name = document.getElementById('employeeName').value;
            const month = document.getElementById('evaluationMonth').value;
            const summary = document.getElementById('evaluationSummary').value;

            const criteria = [
                'pontualidade', 'prazos', 'qualidade', 'equipe',
                'comunicacao', 'proatividade', 'problemas'
            ];

            const criteriaNames = {
                'pontualidade': 'Pontualidade e assiduidade',
                'prazos': 'Cumprimento de prazos',
                'qualidade': 'Qualidade do trabalho entregue',
                'equipe': 'Trabalho em equipe',
                'comunicacao': 'Comunicação e clareza',
                'proatividade': 'Proatividade',
                'problemas': 'Capacidade de resolver problemas'
            };

            let totalWeight = 0;
            let totalResult = 0;
            const evaluationCriteria = [];

            criteria.forEach(criterion => {
                const weight = parseFloat(document.querySelector(`input[data-criteria="${criterion}"].weight-input`).value);
                const score = parseFloat(document.querySelector(`input[data-criteria="${criterion}"].score-input`).value);
                const observation = document.querySelector(`input[data-criteria="${criterion}"].obs-input`).value;
                const result = weight * score;

                evaluationCriteria.push({
                    name: criteriaNames[criterion],
                    weight: weight,
                    score: score,
                    result: result.toFixed(1),
                    observation: observation
                });

                totalWeight += weight;
                totalResult += result;
            });

            const evaluation = {
                name: name,
                month: month,
                criteria: evaluationCriteria,
                totalWeight: totalWeight,
                totalResult: totalResult.toFixed(1),
                average: (totalResult / totalWeight).toFixed(1),
                summary: summary,
                timestamp: new Date()
            };

            evaluations.unshift(evaluation);
            renderEvaluations();

            // Reset form
            this.reset();
            updateCalculations();

            // Scroll to evaluations
            document.querySelector('.evaluations-section').scrollIntoView({
                behavior: 'smooth'
            });
        });

        // Initialize
        updateCalculations();
        renderEvaluations();
    </script>
</body>

</html>