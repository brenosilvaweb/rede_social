<?php
session_start();
include("conexao.php");

// Pegar ID do usuário atual
$nome_usuario = $_SESSION["usuario"];
$sql_usuario = "SELECT * FROM usuarios WHERE nome = '$nome_usuario'";
$res = $conn->query($sql_usuario);
$usuario = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cidade = $_POST["cidade"];
    $bio = $_POST["bio"];
    $link_instagram = $_POST["link_instagram"];
    $link_twitter = $_POST["link_twitter"];
    $link_outra = $_POST["link_outra"];

    // Upload da imagem
    if ($_FILES["foto"]["name"]) {
        $nome_arquivo = time() . "_" . $_FILES["foto"]["name"];
        $caminho = "uploads/" . $nome_arquivo;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
    } else {
        $nome_arquivo = $usuario["foto_perfil"]; // mantém a anterior se não mudar
    }

    $id_usuario = $usuario["id"];
    $sql_update = "UPDATE usuarios SET 
        cidade='$cidade', 
        bio='$bio', 
        link_instagram='$link_instagram', 
        link_twitter='$link_twitter', 
        link_outra='$link_outra',
        foto_perfil='$nome_arquivo'
        WHERE id=$id_usuario";

    if ($conn->query($sql_update)) {
        echo "<script>alert('Perfil atualizado com sucesso!');</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Segoe UI", Roboto, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 40px auto;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #1877f2;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .foto-preview {
            text-align: center;
            margin-bottom: 25px;
        }

        .foto-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #1877f2;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 250px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="text"],
        input[type="file"],
        textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 80px;
        }

        button {
            align-self: center;
            width: 200px;
            margin-top: 20px;
            padding: 12px;
            background-color: #1877f2;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #145db2;
        }

        @media (max-width: 600px) {
            .form-group {
                min-width: 100%;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>

        <div class="foto-preview">
            <img src="<?php echo !empty($usuario['foto_perfil']) ? 'uploads/' . htmlspecialchars($usuario['foto_perfil']) : 'img/user.png'; ?>" alt="Foto de Perfil">
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Foto de Perfil:</label>
                    <input type="file" name="foto">
                </div>

                <div class="form-group">
                    <label>Cidade:</label>
                    <input type="text" name="cidade" value="<?php echo htmlspecialchars($usuario["cidade"] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1 1 100%;">
                    <label>Bio:</label>
                    <textarea name="bio"><?php echo htmlspecialchars($usuario["bio"] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Instagram:</label>
                    <input type="text" name="link_instagram" value="<?php echo htmlspecialchars($usuario["link_instagram"] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Twitter:</label>
                    <input type="text" name="link_twitter" value="<?php echo htmlspecialchars($usuario["link_twitter"] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Outro link:</label>
                    <input type="text" name="link_outra" value="<?php echo htmlspecialchars($usuario["link_outra"] ?? ''); ?>">
                </div>
            </div>

            <button type="submit">Salvar</button>
        </form>


        <a href="home.php">
            <img src="img/voltar.png" alt="Voltar" style="position: fixed; bottom: 30px; right: 30px; height: 50px;">
        </a>
    </div>
</body>
</html>
