<?php
session_start();
include("conexao.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Buscar dados do usuário logado
$stmtUser = $conn->prepare("SELECT nome, foto_perfil, cidade, bio FROM usuarios WHERE id = ?");
$stmtUser->bind_param("i", $usuario_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$usuario = $resultUser->fetch_assoc();
$stmtUser->close();

// Excluir postagem
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excluir_postagem'])) {
    $postagem_id = $_POST['postagem_id'];

    // Verifica se a postagem pertence ao usuário logado
    $stmtCheck = $conn->prepare("SELECT imagem FROM postagens WHERE id = ? AND usuario_id = ?");
    $stmtCheck->bind_param("ii", $postagem_id, $usuario_id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $postagem = $resultCheck->fetch_assoc();

        if (!empty($postagem['imagem']) && file_exists("uploads/" . $postagem['imagem'])) {
            unlink("uploads/" . $postagem['imagem']);
        }

        $stmtDelete = $conn->prepare("DELETE FROM postagens WHERE id = ?");
        $stmtDelete->bind_param("i", $postagem_id);
        $stmtDelete->execute();
        $stmtDelete->close();
    }

    $stmtCheck->close();
    header("Location: postagens.php");
    exit;
}

// Criar nova postagem
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["texto"])) {
    $texto = $_POST["texto"] ?? '';
    $imagem = "";

    if (!empty($_FILES["imagem"]["name"]) && $_FILES["imagem"]["error"] == 0) {
        $nomeImagem = time() . "_" . basename($_FILES["imagem"]["name"]);
        $caminhoImagem = "uploads/" . $nomeImagem;
        move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoImagem);
        $imagem = $nomeImagem;
    }

    $stmt = $conn->prepare("INSERT INTO postagens (usuario_id, texto, imagem) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $texto, $imagem);
    $stmt->execute();
    $stmt->close();

    header("Location: postagens.php");
    exit;
}

// Buscar postagens
$sql = "SELECT p.*, u.nome, u.foto_perfil, u.cidade, u.bio 
        FROM postagens p
        JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.data_postagem DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Feed de Postagens</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background-color: #e9ebee;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #1877f2;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-info small {
            font-weight: normal;
            font-size: 12px;
        }

        .container {
            margin: 30px auto;
            padding: 0 15px;
        }

        .postar {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .postar textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            resize: none;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .postar input[type="file"] {
            margin-bottom: 10px;
        }

        .postar button {
            background-color: #1877f2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .posts-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .post {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            width: 100%;
            max-width: 320px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex: 1 1 300px;
            position: relative;
        }

        .perfil {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .perfil img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .perfil-info {
            font-size: 14px;
        }

        .perfil-info strong {
            font-size: 16px;
            color: #1877f2;
        }

        .perfil-info small {
            color: #555;
        }

        .post img {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 10px;
        }

        .post p {
            margin-top: 10px;
            font-size: 15px;
        }

        .post form button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .post {
                max-width: 100%;
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<header>
    <img src="<?php echo !empty($usuario['foto_perfil']) ? 'uploads/' . htmlspecialchars($usuario['foto_perfil']) : 'img/user.png'; ?>" alt="Foto de perfil">
    <div class="user-info">
        <span><?php echo htmlspecialchars($usuario['nome']); ?></span>
        <small><?php echo htmlspecialchars($usuario['cidade']); ?> • <?php echo htmlspecialchars($usuario['bio']); ?></small>
    </div>
</header>

<div class="container">

    <div class="postar">
        <form method="post" enctype="multipart/form-data">
            <textarea name="texto" placeholder="No que você está pensando?" required></textarea>
            <input type="file" name="imagem">
            <button type="submit">Publicar</button>
        </form>
    </div>

    <div class="posts-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <div class="perfil">
                        <img src="<?php echo !empty($row['foto_perfil']) ? 'uploads/' . htmlspecialchars($row['foto_perfil']) : 'img/user.png'; ?>" alt="Foto de perfil">
                        <div class="perfil-info">
                            <strong><?php echo htmlspecialchars($row['nome']); ?></strong><br>
                            <small><?php echo htmlspecialchars($row['cidade'] ?? ''); ?> • <?php echo date('d/m/Y H:i', strtotime($row['data_postagem'])); ?></small><br>
                            <small><?php echo htmlspecialchars($row['bio'] ?? ''); ?></small>
                        </div>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($row['texto'])); ?></p>
                    <?php if (!empty($row['imagem'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['imagem']); ?>" alt="Imagem da postagem">
                    <?php endif; ?>

                    <?php if ($row['usuario_id'] == $usuario_id): ?>
                        <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir esta postagem?');">
                            <input type="hidden" name="postagem_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="excluir_postagem">Excluir</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma postagem ainda.</p>
        <?php endif; ?>
    </div>

    <a href="home.php">
        <img src="img/voltar.png" alt="Voltar" style="position: fixed; bottom: 30px; right: 30px; height: 50px;">
    </a>

</div>

</body>
</html>
