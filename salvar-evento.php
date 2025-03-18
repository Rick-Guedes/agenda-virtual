<?php
header('Content-Type: application/json');

// Configurações do banco de dados
require_once "/config.php";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die(json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $conn->connect_error]));
}

// Receber os dados do front-end
$data = json_decode(file_get_contents('php://input'), true);

// Verificar se os dados estão sendo recebidos corretamente
if (!$data) {
    echo json_encode(['error' => 'Dados não recebidos ou formato inválido.']);
    exit;
}

// Verificar campos obrigatórios
if (empty($data['data']) || empty($data['titulo']) || empty($data['horario']) || empty($data['email']) || empty($data['nome_completo'])) {
    echo json_encode(['error' => 'Todos os campos obrigatórios devem ser preenchidos.']);
    exit;
}

// Dados recebidos
$dataEvento = $data['data'];     // Exemplo: "2025-02-21"
$horarioNovo = $data['horario']; // Exemplo: "14:30"

// Garantir que o horário esteja no formato correto (HH:MM:SS)
$horarioNovo = date('H:i:s', strtotime($horarioNovo)); // Converte para "14:30:00"

// Calcular os limites de 3 horas antes e depois
$horarioMinimo = date('H:i:s', strtotime($horarioNovo . ' -3 hours')); // Exemplo: "11:30:00"
$horarioMaximo = date('H:i:s', strtotime($horarioNovo . ' +3 hours')); // Exemplo: "17:30:00"

// Depuração: exibir os valores calculados
error_log("Data: $dataEvento, Horário Novo: $horarioNovo, Mínimo: $horarioMinimo, Máximo: $horarioMaximo");

// Verificar se já existe um evento na mesma data dentro da janela de 3 horas
$checkQuery = $conn->prepare('
    SELECT id, horario 
    FROM eventos 
    WHERE data = ? 
    AND horario BETWEEN ? AND ?
');
$checkQuery->bind_param('sss', $dataEvento, $horarioMinimo, $horarioMaximo);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    // Depuração: listar os eventos encontrados
    $eventosConflitantes = [];
    while ($row = $checkResult->fetch_assoc()) {
        $eventosConflitantes[] = $row['horario'];
    }
    error_log("Eventos conflitantes encontrados: " . implode(", ", $eventosConflitantes));
    echo json_encode(['error' => 'Já existe um evento agendado nesta data dentro de uma janela de 3 horas. Escolha um horário com pelo menos 3 horas de diferença.']);
    exit;
}

// Inserir o evento no banco de dados
$query = $conn->prepare('
    INSERT INTO eventos 
    (data, titulo, horario, email, dados_contato, nome_completo, telefone_whatsapp, publico_reserva, cargo, curso, estúdio, duracao, quantidade_pessoas, necessita_edicao, entrega_edicao) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$query->bind_param('sssssssssssssss', 
    $data['data'], 
    $data['titulo'], 
    $data['horario'], 
    $data['email'], 
    $data['dados_contato'], 
    $data['nome_completo'], 
    $data['telefone_whatsapp'], 
    $data['publico_reserva'], 
    $data['cargo'], 
    $data['curso'], 
    $data['estúdio'], 
    $data['duracao'], 
    $data['quantidade_pessoas'], 
    $data['necessita_edicao'], 
    $data['entrega_edicao']
);

if ($query->execute()) {
    echo json_encode(['message' => 'Evento salvo com sucesso!']);
} else {
    echo json_encode(['error' => 'Erro ao salvar evento: ' . $conn->error]);
}

$conn->close();
?>