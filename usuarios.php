<?php
include("conexao.php");

$sql = "SELECT * FROM usuarios ORDER BY data_criacao DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #1877f2;
            margin-bottom: 30px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .usuario {
            background: white;
            border-radius: 12px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.2s;
        }

        .usuario:hover {
            transform: translateY(-4px);
        }

        .usuario img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #1877f2;
            margin-bottom: 15px;
        }

        .info {
            text-align: center;
        }

        .info h3 {
            margin: 10px 0 5px;
            color: #333;
        }

        .info p {
            margin: 5px 0;
            color: #555;
        }

        .redes {
            margin-top: 10px;
        }

        .redes a {
            text-decoration: none;
            margin: 0 5px;
            color: #1877f2;
            font-weight: bold;
        }

        .redes a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .usuario {
                width: 100%;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>

    <h1>Lista de Usuários</h1>

    <div class="container">
        <?php while($usuario = $result->fetch_assoc()): ?>
            <div class="usuario">
                <img src="uploads/<?php echo $usuario['foto_perfil'] ?: 'default.png'; ?>" alt="Foto de Perfil">
                <div class="info">
                    <h3><?php echo htmlspecialchars($usuario['nome']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($usuario['cidade']); ?></p>
                    <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($usuario['bio'])); ?></p>
                    <div class="redes">
                        <?php if ($usuario['link_instagram']): ?>
                            <a href="<?php echo $usuario['link_instagram']; ?>" target="_blank">Instagram</a>
                        <?php endif; ?>
                        <?php if ($usuario['link_twitter']): ?>
                            <a href="<?php echo $usuario['link_twitter']; ?>" target="_blank">Twitter</a>
                        <?php endif; ?>
                        <?php if ($usuario['link_outra']): ?>
                            <a href="<?php echo $usuario['link_outra']; ?>" target="_blank">Outro</a>
                        <?php endif; ?>
                    </div>
                    <p><small><strong>Entrou em:</strong> <?php echo date("d/m/Y H:i", strtotime($usuario['data_criacao'])); ?></small></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <a href="home.php">
        <img src="img/voltar.png" alt="Voltar" style="position: fixed; bottom: 30px; right: 30px; height: 50px;">
    </a>


</body>
</html>
