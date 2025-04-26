<?php
include("conexao.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($senha, $usuario["senha"])) {
            $_SESSION["usuario"] = $usuario["nome"];
            $_SESSION["usuario_id"] = $usuario["id"]; // ← ESSENCIAL para postagens.php
            header("Location: home.php");
            exit;
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="img/login.png">
</head>
<body>
<form method="post">
    <h2>
        <img src="img/login.png" alt="">
        Login
    </h2>

    <input type="email" name="email" required placeholder="Seu Email"><br>
    <input type="password" name="senha" required placeholder="Sua Senha"><br>

    <button type="submit">Entrar</button>
    <p>Não tem conta? <a href="cadastro.php">Crie uma</a></p>

    <?php if (!empty($erro)): ?>
        <p style="color:red;"><?php echo $erro; ?></p>
    <?php endif; ?>
</form>
</body>
</html>
