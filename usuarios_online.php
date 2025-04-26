<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("conexao.php");

// Define o tempo limite para considerar um usuário "online" (5 minutos)
$tempoLimite = date("Y-m-d H:i:s", strtotime("-5 minutes"));

// Atualiza a última atividade do usuário logado
if (isset($_SESSION["usuario_id"])) {
    $usuario_id = $_SESSION["usuario_id"];
    $stmt = $conn->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Busca usuários online (exceto o próprio)
$sql = "SELECT id, nome FROM usuarios WHERE ultimo_acesso >= ? AND id != ?";
$stmt = $conn->prepare($sql);
$usuariosOnline = [];

if ($stmt) {
    $stmt->bind_param("si", $tempoLimite, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $usuariosOnline[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usuários Online</title>
</head>
<body>
    <h2>Usuários Online</h2>

    <?php if (empty($usuariosOnline)): ?>
        <p>Nenhum usuário online agora.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($usuariosOnline as $usuario): ?>
                <li>
                    <?php echo htmlspecialchars($usuario["nome"]); ?> 
                    - <a href="mensagem.php?para=<?php echo $usuario['id']; ?>">Enviar mensagem</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
