<?php
include("conexao.php");

$mensagem = ""; // variável para guardar a mensagem

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

    if ($conn->query($sql) === TRUE) {
        $mensagem = "Conta criada com sucesso! <a href='login.php'>Entrar</a>";
    } else {
        $mensagem = "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>
    <link rel="stylesheet" href="cadastro.css">
    <link rel="icon" href="img/criarconta.png">
</head>
<body>

<form method="post">
    <h2>
        <img src="img/criarconta.png" alt="">
        cadastro
    </h2>

    <input type="text" name="nome" required placeholder="Seu Nome" autocomplete="new-password"><br>
    <input type="email" name="email" required placeholder="Seu Email" autocomplete="new-password"><br>
    <input type="password" name="senha" required placeholder="Sua Senha" autocomplete="new-password"><br>
    <p>Já tem conta? <a href="login.php">Faça Login</a></p>
    <button type="submit">Cadastrar</button>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?php echo $mensagem; ?></div>
    <?php endif; ?>
</form>

</body>
</html>
