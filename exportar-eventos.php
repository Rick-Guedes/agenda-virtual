<?php
// Configurações do banco de dados
require_once "/config.php";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Consulta para buscar todos os eventos
$query = "SELECT * FROM eventos ORDER BY data, horario";
$result = $conn->query($query);

// Definir cabeçalhos para download do arquivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="eventos_' . date('Y-m-d_H-i-s') . '.csv"');

// Abrir a saída como arquivo CSV
$output = fopen('php://output', 'w');

// Adicionar codificação UTF-8 com BOM para suportar caracteres especiais no Excel
fputs($output, "\xEF\xBB\xBF");

// Definir os cabeçalhos da tabela (colunas)
$colunas = [
    'ID',
    'Data',
    'Título',
    'Horário',
    'E-mail',
    'Dados de Contato',
    'Nome Completo',
    'Telefone/WhatsApp',
    'Público Reserva',
    'Cargo',
    'Curso',
    'Estúdio',
    'Duração',
    'Quantidade de Pessoas',
    'Necessita Edição',
    'Entrega da Edição'
];
fputcsv($output, $colunas, ';'); // Usar ';' como delimitador (padrão no Excel BR)

// Adicionar os dados dos eventos
while ($row = $result->fetch_assoc()) {
    $linha = [
        $row['id'],
        $row['data'],
        $row['titulo'],
        $row['horario'],
        $row['email'],
        $row['dados_contato'],
        $row['nome_completo'],
        $row['telefone_whatsapp'],
        $row['publico_reserva'],
        $row['cargo'],
        $row['curso'],
        $row['estúdio'],
        $row['duracao'],
        $row['quantidade_pessoas'],
        $row['necessita_edicao'],
        $row['entrega_edicao']
    ];
    fputcsv($output, $linha, ';');
}

// Fechar o arquivo e a conexão
fclose($output);
$conn->close();
exit;
?>