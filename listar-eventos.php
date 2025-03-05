<!-- 
 Autor: Flavio Henrique Guedes Nobre;
 Version: 1.0.1
 "Você é livre para usar e modificar com sabedoria esse código ele é aberto, só não deixe de 
 dar os créditos ao autor"
-->
 
<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'calendario';

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die(json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $conn->connect_error]));
}

// Buscar todos os eventos
$query = "SELECT id, data, titulo, horario FROM eventos";
$result = $conn->query($query);

$eventos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'data' => $row['data'],          // Formato: "YYYY-MM-DD"
            'titulo' => $row['titulo'],
            'horario' => $row['horario']     // Formato: "HH:MM:SS"
        ];
    }
}

// Retornar os eventos como JSON
echo json_encode($eventos);

$conn->close();
?>