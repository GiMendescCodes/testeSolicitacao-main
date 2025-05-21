<?php
$conn = new mysqli('localhost', 'root', '', 'solicitacao');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$mensagem_sucesso = '';
$historico = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    $STATUS = 'pendente';
    $data_envio = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO dados (nome, email, mensagem, STATUS, data_envio) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $email, $mensagem, $STATUS, $data_envio);
    $stmt->execute();

    $mensagem_sucesso = 'Solicitação enviada com sucesso!';

    // Buscar histórico do mesmo email
    $stmt = $conn->prepare("SELECT * FROM dados WHERE email = ? ORDER BY data_envio DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $historico = $stmt->get_result();
}
?>

<h1>Formulário de Solicitação</h1>

<form method="POST">
    <label>Nome: <input type="text" name="nome" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Mensagem: <textarea name="mensagem" required></textarea></label><br>
    <button type="submit">Enviar</button>
</form>

<?php if ($mensagem_sucesso): ?>
    <p><?php echo $mensagem_sucesso; ?></p>

    <h2>Histórico de Solicitações</h2>
    <ul>
        <?php while ($dado = $historico->fetch_assoc()): ?>
            <li>
    <strong>Data:</strong> <?php echo $dado['data_envio']; ?> |
    
    <strong>Status:</strong>
    <?php 
        /* $status = isset($dado['STATUS']) ? $dado['STATUS'] : 'pendente';  */
            $status = $dado['STATUS'];
        $imagem = '';
        if($status== 'pendente'){
            $imagem = 'img/pendente.png';
        } elseif($status == 'aceito'){
            $imagem = 'img/aceito.png';
        } elseif($status == 'negado'){
            $imagem = 'img/negado.png';
        }
    ?>
        <img src="<?php echo $imagem; ?>" style="width:30px; margin-left:5px;" />
    
    <strong>Mensagem:</strong> <?php echo htmlspecialchars($dado['mensagem']); ?>
</li>

        <?php endwhile; ?>
    </ul>
<?php endif; ?>
