<?php
session_start();
include("conexao.php");

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["mensagens" => []]);
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

$sql = "SELECT m.id, m.mensagem, m.data_envio, m.remetente_id, u.nome
        FROM mensagens m
        JOIN usuarios u ON m.remetente_id = u.id
        WHERE m.destinatario_id = ?
        ORDER BY m.data_envio DESC
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$mensagens = [];

while ($row = $result->fetch_assoc()) {
    $mensagens[] = [
        "id" => $row["id"],
        "mensagem" => substr($row["mensagem"], 0, 60) . "...",
        "data" => date('d/m/Y H:i', strtotime($row["data_envio"])),
        "remetente_id" => $row["remetente_id"],
        "nome" => $row["nome"]
    ];
}

echo json_encode(["mensagens" => $mensagens]);
?>
