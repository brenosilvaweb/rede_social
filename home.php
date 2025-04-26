<?php
session_start();
include("conexao.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

$nome = $_SESSION["usuario"];
$usuario_id = $_SESSION["usuario_id"];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - <?php echo $nome; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        header {
            background-color: #1877f2;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        header h1 {
            font-size: 20px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-left: 15px;
        }

        .content {
            padding: 30px 20px;
            text-align: center;
        }

        .content h2 {
            margin-bottom: 30px;
            color: #333;
        }

        #menu {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        #menu a {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 150px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            transition: transform 0.2s ease;
        }

        #menu a:hover {
            transform: translateY(-5px);
        }

        #menu img {
            width: 40px;
            margin-bottom: 10px;
        }

        .mensagens {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 5px #ccc;
            max-width: 600px;
            margin: 0 auto;
        }

        .mensagem {
            text-align: left;
            margin-bottom: 15px;
        }

        .mensagem strong {
            color: #333;
        }

        .mensagem small {
            color: #777;
        }

        .mensagem a {
            text-decoration: none;
            color: #1877f2;
            font-weight: bold;
        }

        .mensagem.nova-mensagem {
            background-color: #e0f7fa;
            border-left: 4px solid #00acc1;
            padding: 10px;
            border-radius: 6px;
        }

        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 10px 0;
        }

        @media (max-width: 600px) {
            #menu a {
                width: 100%;
                max-width: 300px;
            }

            header {
                flex-direction: column;
                align-items: flex-start;
            }

            header h1, header a {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Olá, <?php echo htmlspecialchars($nome); ?>!</h1>
    <nav>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="content">
    <h2>O que deseja fazer hoje?</h2>
    <section id="menu">
        <a href="postagens.php">
            <img src="img/login.png" alt="Postar">
            <div>Criar Postagem</div>
        </a>
        <a href="editar_perfil.php">
            <img src="img/login.png" alt="Perfil">
            <div>Editar Perfil</div>
        </a>
        <a href="usuarios.php">
            <img src="img/login.png" alt="Usuários">
            <div>Ver Usuários</div>
        </a>
        <a href="cadastro.php">
            <img src="img/login.png" alt="Conta">
            <div>Acessar Outra Conta</div>
        </a>
        <a href="mensagem.php">
            <img src="img/login.png" alt="Conta">
            <div>Enviar Mensagem</div>
        </a>
    </section>

    <div class="mensagens">
        <h3>📩 Mensagens Recentes</h3>
        <div id="mensagens">
            <p>Carregando mensagens...</p>
        </div>
    </div>
</div>

<audio id="audioNotificacao" src="notificacao.mp3" preload="auto"></audio>

<script>
    let ultimaMensagemId = 0;

    function atualizarMensagens() {
        fetch("atualizar_mensagens.php")
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById("mensagens");
                container.innerHTML = "";

                if (data.mensagens.length === 0) {
                    container.innerHTML = "<p>Você ainda não recebeu nenhuma mensagem.</p>";
                    return;
                }

                const maisRecente = data.mensagens[0];

                if (maisRecente.id > ultimaMensagemId && ultimaMensagemId !== 0) {
                    document.getElementById("audioNotificacao").play();
                }

                ultimaMensagemId = maisRecente.id;

                data.mensagens.forEach((msg, index) => {
                    let div = document.createElement("div");
                    div.classList.add("mensagem");

                    if (index === 0 && msg.id === ultimaMensagemId) {
                        div.classList.add("nova-mensagem");
                    }

                    div.innerHTML = `
                        <strong>${msg.nome}:</strong> ${msg.mensagem}<br>
                        <small>${msg.data}</small><br>
                        <a href="conversa.php?id=${msg.remetente_id}">Responder</a>
                    `;
                    container.appendChild(div);
                    if (index < data.mensagens.length - 1) {
                        container.appendChild(document.createElement("hr"));
                    }
                });
            })
            .catch(error => {
                console.error("Erro ao buscar mensagens:", error);
            });
    }

    atualizarMensagens();
    setInterval(atualizarMensagens, 5000);
</script>

</body>
</html>
