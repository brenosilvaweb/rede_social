<?php
session_start();
include("conexao.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$remetente_id = $_SESSION["usuario_id"];
$destinatario_id = $_GET["id"] ?? 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensagem = trim($_POST["mensagem"]);
    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $remetente_id, $destinatario_id, $mensagem);
        $stmt->execute();
        $stmt->close();
        header("Location: conversa.php?id=" . $destinatario_id);
        exit;
    }
}

$stmtNome = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmtNome->bind_param("i", $destinatario_id);
$stmtNome->execute();
$stmtNome->bind_result($nome_destinatario);
$stmtNome->fetch();
$stmtNome->close();

$stmt = $conn->prepare("SELECT * FROM mensagens WHERE 
    (remetente_id = ? AND destinatario_id = ?) OR 
    (remetente_id = ? AND destinatario_id = ?)
    ORDER BY data_envio ASC");
$stmt->bind_param("iiii", $remetente_id, $destinatario_id, $destinatario_id, $remetente_id);
$stmt->execute();
$mensagens = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversa com <?php echo htmlspecialchars($nome_destinatario); ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #ece5dd;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }

        .chat-header {
            background-color: #075e54;
            color: white;
            padding: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 80%;
            padding: 10px;
            border-radius: 10px;
            position: relative;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .from-me {
            background-color: #dcf8c6;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }

        .from-them {
            background-color: #fff;
            align-self: flex-start;
            border-bottom-left-radius: 0;
            border: 1px solid #ddd;
        }

        .timestamp {
            font-size: 11px;
            color: #666;
            text-align: right;
            margin-top: 5px;
        }

        .chat-form {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ccc;
            background-color: #f0f0f0;
        }

        .chat-form textarea {
            flex-grow: 1;
            resize: none;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            height: 50px;
            width: 100%;
        }

        .chat-form button {
            background-color: #075e54;
            color: white;
            border: none;
            padding: 0 15px;
            margin-left: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        a.voltar {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .chat-header {
                font-size: 14px;
                padding: 12px;
            }

            .chat-form textarea {
                font-size: 13px;
                height: 45px;
            }

            .chat-form button {
                padding: 0 12px;
                font-size: 13px;
            }

            .message {
                font-size: 13px;
            }

            .timestamp {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <span>Conversa com <?php echo htmlspecialchars($nome_destinatario); ?></span>
            <a href="mensagem.php" class="voltar">← Voltar</a>
        </div>

        <div class="chat-messages" id="mensagens">
            <?php while ($row = $mensagens->fetch_assoc()): ?>
                <div class="message <?php echo $row["remetente_id"] == $remetente_id ? 'from-me' : 'from-them'; ?>">
                    <?php echo nl2br(htmlspecialchars($row["mensagem"])); ?>
                    <div class="timestamp">
                        <?php echo date("d/m/Y H:i", strtotime($row["data_envio"])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <form class="chat-form" method="post">
            <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>
            <button type="submit">Enviar</button>
        </form>
    </div>

    <script>
        const mensagens = document.getElementById("mensagens");
        mensagens.scrollTop = mensagens.scrollHeight;
    </script>
</body>
</html>
