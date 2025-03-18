<?php
// Configurações de depuração (remover em produção)
ini_set('display_errors', 0); // Desativar exibição de erros em produção
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE); // Mostrar erros, mas não notices

// Definir o tipo de conteúdo como JSON (sem saída antes disso)
header('Content-Type: application/json');

// Configurações do banco de dados
require_once "/config.php";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $conn->connect_error]);
    exit;
}

// Buscar todos os eventos
$query = "SELECT id, data, titulo, horario FROM eventos";
$result = $conn->query($query);

if ($result === false) {
    echo json_encode(['error' => 'Erro na consulta SQL: ' . $conn->error]);
    exit;
}

$eventos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'data' => $row['data'],
            'titulo' => $row['titulo'],
            'horario' => $row['horario']
        ];
    }
} else {
    echo json_encode([]); // Retornar array vazio se não houver eventos
}

// Fechar conexão e retornar JSON
$conn->close();
echo json_encode($eventos);
?>