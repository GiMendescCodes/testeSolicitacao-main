<?php
include 'conexao.php';

$id_func = $_POST['funcionario_id'];
$mes = $_POST['mes_ano'];

$campos = ["pontualidade", "prazos", "qualidade", "equipe", "comunicacao", "proatividade"];
$valores = [];
$peso_total = 0;
$soma_pesos = 0;

// coletar notas, pesos e observações individuais
foreach ($campos as $c) {
    $nota = $_POST[$c];
    $peso = $_POST["peso_$c"];
    $obs = $_POST["obs_$c"] ?? ''; // observação individual

    $valores[$c] = $nota;
    $valores["peso_$c"] = $peso;
    $valores["obs_$c"] = $obs;

    $soma_pesos += $peso;
    $peso_total += $nota * $peso;
}

$media = $peso_total / $soma_pesos;
$resumo = $_POST['resumo'] ?? '';

// Verifica se já existe avaliação para funcionário e mês
$check = $pdo->prepare("SELECT COUNT(*) FROM desempenho WHERE funcionario_id = ? AND mes_ano = ?");
$check->execute([$id_func, $mes]);
$existe = $check->fetchColumn();

if ($existe) {
    // Atualiza
    $stmt = $pdo->prepare("UPDATE desempenho SET
        pontualidade = ?, prazos = ?, qualidade = ?, equipe = ?, comunicacao = ?, proatividade = ?,
        peso_pontualidade = ?, peso_prazos = ?, peso_qualidade = ?, peso_equipe = ?, peso_comunicacao = ?, peso_proatividade = ?,
        obs_pontualidade = ?, obs_prazos = ?, obs_qualidade = ?, obs_equipe = ?, obs_comunicacao = ?, obs_proatividade = ?,
        resumo = ?, media_mes = ?
        WHERE funcionario_id = ? AND mes_ano = ?
    ");
    $stmt->execute([
        $valores['pontualidade'], $valores['prazos'], $valores['qualidade'],
        $valores['equipe'], $valores['comunicacao'], $valores['proatividade'],
        $valores['peso_pontualidade'], $valores['peso_prazos'], $valores['peso_qualidade'],
        $valores['peso_equipe'], $valores['peso_comunicacao'], $valores['peso_proatividade'],
        $valores['obs_pontualidade'], $valores['obs_prazos'], $valores['obs_qualidade'],
        $valores['obs_equipe'], $valores['obs_comunicacao'], $valores['obs_proatividade'],
        $resumo, $media,
        $id_func, $mes
    ]);
} else {
    // Insere
    $stmt = $pdo->prepare("INSERT INTO desempenho (
        funcionario_id, mes_ano,
        pontualidade, prazos, qualidade, equipe, comunicacao, proatividade,
        peso_pontualidade, peso_prazos, peso_qualidade, peso_equipe, peso_comunicacao, peso_proatividade,
        obs_pontualidade, obs_prazos, obs_qualidade, obs_equipe, obs_comunicacao, obs_proatividade,
        resumo, media_mes
    ) VALUES (
        ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?
    )");

    $stmt->execute([
        $id_func, $mes,
        $valores['pontualidade'], $valores['prazos'], $valores['qualidade'],
        $valores['equipe'], $valores['comunicacao'], $valores['proatividade'],
        $valores['peso_pontualidade'], $valores['peso_prazos'], $valores['peso_qualidade'],
        $valores['peso_equipe'], $valores['peso_comunicacao'], $valores['peso_proatividade'],
        $valores['obs_pontualidade'], $valores['obs_prazos'], $valores['obs_qualidade'],
        $valores['obs_equipe'], $valores['obs_comunicacao'], $valores['obs_proatividade'],
        $resumo, $media
    ]);
}

// Redireciona para evitar reenvio e manter seleção
echo "<form id='redirectForm' method='post' action='index.php'>
        <input type='hidden' name='funcionario_id' value='$id_func'>
      </form>
      <script>document.getElementById('redirectForm').submit();</script>";
exit;
?>
