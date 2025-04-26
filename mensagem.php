<?php
session_start();
include("conexao.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];
$termo = "%" . ($_GET["buscar"] ?? '') . "%";

$sql = "SELECT id, nome, email, cidade, bio, link_instagram, link_twitter, link_outra, foto_perfil, data_criacao FROM usuarios WHERE nome LIKE ? AND id != ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar a query: " . $conn->error);
}

$stmt->bind_param("si", $termo, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Buscar Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ebee;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #1877f2;
            text-align: center;
        }

        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form input[type="text"] {
            padding: 10px;
            width: 80%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 20px;
            outline: none;
        }

        .search-form button {
            padding: 10px 16px;
            border: none;
            background-color: #1877f2;
            color: white;
            border-radius: 20px;
            cursor: pointer;
            margin-left: 5px;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .user-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
            box-sizing: border-box;
            transition: transform 0.2s;
        }

        .user-card:hover {
            transform: scale(1.02);
        }

        .user-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #1877f2;
        }

        .user-info {
            text-align: center;
        }

        .user-info h3 {
            margin: 0 0 5px;
            color: #1877f2;
            font-size: 18px;
        }

        .user-info p {
            margin: 4px 0;
            font-size: 14px;
        }

        .links {
            margin: 8px 0;
        }

        .links a {
            margin: 0 5px;
            text-decoration: none;
            color: #555;
            font-size: 13px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .chat-link {
            color: white;
            background-color: #42b72a;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
            font-size: 14px;
        }

        .chat-link:hover {
            background-color: #36a420;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .search-form input[type="text"] {
                width: 100%;
                max-width: none;
            }

            .search-form button {
                width: 100%;
                margin-top: 10px;
            }

            .user-card {
                width: 100%;
                max-width: 100%;
            }

            .user-info h3 {
                font-size: 16px;
            }

            .user-info p {
                font-size: 13px;
            }

            .links a {
                font-size: 12px;
            }

            .chat-link {
                font-size: 13px;
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
    <h2>Procurar Usuários</h2>

    <form method="get" class="search-form">
        <input type="text" name="buscar" placeholder="Buscar usuário" value="<?php echo htmlspecialchars($_GET['buscar'] ?? '') ?>">
        <button type="submit">Procurar</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="cards-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="user-card">
                    <img src="uploads/<?php echo htmlspecialchars($row['foto_perfil']); ?>" alt="Foto de <?php echo htmlspecialchars($row['nome']); ?>">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Cidade:</strong> <?php echo htmlspecialchars($row['cidade']); ?></p>
                        <p><strong>Bio:</strong> <?php echo htmlspecialchars($row['bio']); ?></p>
                        <p><strong>Desde:</strong> <?php echo date('d/m/Y', strtotime($row['data_criacao'])); ?></p>
                        <div class="links">
                            <?php if (!empty($row['link_instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($row['link_instagram']); ?>" target="_blank">Instagram</a>
                            <?php endif; ?>
                            <?php if (!empty($row['link_twitter'])): ?>
                                <a href="<?php echo htmlspecialchars($row['link_twitter']); ?>" target="_blank">Twitter</a>
                            <?php endif; ?>
                            <?php if (!empty($row['link_outra'])): ?>
                                <a href="<?php echo htmlspecialchars($row['link_outra']); ?>" target="_blank">Outra</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a class="chat-link" href="conversa.php?id=<?php echo $row["id"]; ?>">Conversar</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center;">Nenhum usuário encontrado.</p>
    <?php endif; ?>
</body>
</html>
