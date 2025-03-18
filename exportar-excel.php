<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "/config.php";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar todos os eventos
$query = "SELECT * FROM eventos";
$result = $conn->query($query);

// Configurar cabeçalhos para download do Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="eventos.xls"');

// Gerar conteúdo do Excel (formato HTML simples compatível com Excel)
echo "<table border='1'>";
echo "<tr>
    <th>ID</th>
    <th>Data</th>
    <th>Título</th>
    <th>Horário</th>
    <th>Email</th>
    <th>Dados Contato</th>
    <th>Nome Completo</th>
    <th>Telefone/WhatsApp</th>
    <th>Público Reserva</th>
    <th>Cargo</th>
    <th>Curso</th>
    <th>Estúdio</th>
    <th>Duração</th>
    <th>Quantidade Pessoas</th>
    <th>Necessita Edição</th>
    <th>Entrega Edição</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['data']}</td>";
    echo "<td>{$row['titulo']}</td>";
    echo "<td>{$row['horario']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['dados_contato']}</td>";
    echo "<td>{$row['nome_completo']}</td>";
    echo "<td>{$row['telefone_whatsapp']}</td>";
    echo "<td>{$row['publico_reserva']}</td>";
    echo "<td>{$row['cargo']}</td>";
    echo "<td>{$row['curso']}</td>";
    echo "<td>{$row['estúdio']}</td>";
    echo "<td>{$row['duracao']}</td>";
    echo "<td>{$row['quantidade_pessoas']}</td>";
    echo "<td>{$row['necessita_edicao']}</td>";
    echo "<td>{$row['entrega_edicao']}</td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();
exit;
?>