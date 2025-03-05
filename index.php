<!-- 
 Autor: Flavio Henrique Guedes Nobre;
 Version: 1.0.1
 "Você é livre para usar e modificar com sabedoria esse código ele é aberto, só não deixe de 
 dar os créditos ao autor"
-->
 
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Eventos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <button class="auth-button" onclick="location.href='<?php echo isset($_SESSION['user_id']) ? 'logout.php' : 'login.php'; ?>'">
        <?php echo isset($_SESSION['user_id']) ? 'Sair' : 'Login'; ?>
    </button>

    <div class="calendar-container">
        <div class="calendar">
            <div class="header">
                <button id="prevMonth"><</button>
                <h2 id="currentMonth"></h2>
                <button id="nextMonth">></button>
            </div>
            <div class="days" id="days"></div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="export-button" onclick="location.href='exportar-excel.php'">Exportar para Excel</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para adicionar evento -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <h2>Reserva de Estúdios</h2>
            <form id="eventForm">
                <div class="form-group" style="text-align: justify;"><br>
                    <b>Prazo de tolerância:</b>
                    <li>As reservas do estúdio possuem uma tolerância máxima de 15 minutos antes do cancelamento automático. Evite atrasos e garanta seu agendamento!</li><br>
                    <b>Informação:</b>
                    <li>Sobre as roupas, recomendamos a utilização de roupas sem estampas, brilhos, listras ou com transparência. De preferência tons que vão do médio ao escuro, e tecidos mais lisos. A cor verde não é permitida pois é a mesma utilizada na remoção do fundo no vídeo.</li><br>
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" required>
                </div>
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                <div class="form-group">
                    <label for="horario">Horário:</label>
                    <input type="time" id="horario" name="horario" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="nome_completo">Nome Completo:</label>
                    <input type="text" id="nome_completo" name="nome_completo" required>
                </div>
                <div class="form-group">
                    <label for="telefone_whatsapp">Telefone/WhatsApp:</label>
                    <input type="text" id="telefone_whatsapp" name="telefone_whatsapp">
                </div>
                <div class="form-group">
                    <label for="publico_reserva">Público Reserva:</label>
                    <select id="publico_reserva" name="publico_reserva">
                        <option value="Público interno">Público interno (Alunos, Colaboradores, Coordenadores, Professores e Tutores)</option>
                        <option value="Público Externo">Público Externo (Comunidade, Contratantes)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <select id="cargo" name="cargo">
                        <option value="Aluno">Aluno</option>
                        <option value="Colaborador">Colaborador</option>
                        <option value="Coordenador">Coordenador</option>
                        <option value="Professor">Professor</option>
                        <option value="Tutor">Tutor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="curso">Curso:</label>
                    <select id="curso" name="curso">
                        <option value="Administração">Administração</option>
                        <option value="Agronomia">Agronomia</option>
                        <option value="Biomedicina">Biomedicina</option>
                        <option value="Ciências Contábeis">Ciências Contábeis</option>
                        <option value="Direito">Direito</option>
                        <option value="Educação Física">Educação Física</option>
                        <option value="Enfermagem">Enfermagem</option>
                        <option value="Estética e Cosmética">Estética e Cosmética</option>
                        <option value="Farmácia">Farmácia</option>
                        <option value="Fisioterapia">Fisioterapia</option>
                        <option value="Fonoaudiologia">Fonoaudiologia</option>
                        <option value="Medicina">Medicina</option>
                        <option value="Medicina Veterinária">Medicina Veterinária</option>
                        <option value="Nutrição">Nutrição</option>
                        <option value="Odontologia">Odontologia</option>
                        <option value="Pedagogia">Pedagogia</option>
                        <option value="Psicologia">Psicologia</option>
                        <option value="Terapia Ocupacional">Terapia Ocupacional</option>
                        <option value="Zootecnia">Zootecnia</option>
                        <option value="Não se aplica">Não se aplica</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="estudio">Estúdio:</label>
                    <select id="estudio" name="estúdio">
                        <option value="CromaKey">CromaKey (Estúdio de fundo verde - até 2 pessoas)</option>
                        <option value="PodCast">PodCast (Estúdio com mesacast - até 4 pessoas)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="duracao">Duração:</label>
                    <select id="duracao" name="duracao">
                        <option value="até 30 minutos">até 30 minutos</option>
                        <option value="de 40 a 60 minutos">de 40 a 60 minutos</option>
                        <option value="mais de 60 minutos">mais de 60 minutos</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantidade_pessoas">Quantidade de Pessoas:</label>
                    <select id="quantidade_pessoas" name="quantidade_pessoas">
                        <option value="1 pessoa">1 pessoa</option>
                        <option value="2 pessoas">2 pessoas</option>
                        <option value="3 pessoas">3 pessoas</option>
                        <option value="4 pessoas">4 pessoas</option>
                    </select>
                </div>
                <div class="form-group" style="text-align: justify;">
                    <label for="necessita_edicao">Necessita Edição:</label><br>
                    <li>Para a edição são necessários 05 dias úteis.</li>
                    <li>Se a edição não for necessária, a entrega dos vídeos é imediata (necessita disp. de armazenamento com espaço superior a 30Gb)</li><br>
                    <select id="necessita_edicao" name="necessita_edicao">
                        <option value="Sim">Sim</option>
                        <option value="Não">Não</option>
                    </select>
                </div>
                <div class="form-group" style="text-align: justify;">
                    <label for="entrega_edicao">Entrega da Edição:</label><br>
                    <li>Para retirada do arquivo (necessita disp. de armazenamento com espaço superior a 30Gb)</li>
                    <li>O Upload no YouTube será realizado no Canal UNIVAR</li><br>
                    <select id="entrega_edicao" name="entrega_edicao">
                        <option value="Retirada do arquivo">Retirada do arquivo</option>
                        <option value="Upload no YouTube">Upload no YouTube</option>
                    </select>
                </div><br><br>
                <div class="form-group" style="text-align: justify;color:brown">
                    <label for="dados_contato" style="color:brown">Exclusivo para Público Externo (Que Não é Aluno)</label>
                    <!--<textarea id="dados_contato" name="dados_contato"></textarea>-->                   
                    <p>Para alugar o estúdio, é necessário:</p>
                    <li>Informar os dados de contato (nome, telefone e e-mail);</li>
                    <li>Pagar a taxa de uso de R$ 300,00 (válida para 2 horas, com 30 minutos de tolerância);</li>
                    <li>Assinar o contrato de locação;</li>
                    <li> Caso ultrapasse o tempo contratado, será cobrado um valor adicional de <b>R$ 100,00 por hora excedente.</b></li><br>
                    <label style="color:brown">Somente após a assinatura do contrato, o agendamento será confirmado.</label>
                </div>
                <button type="submit" id="saveEvent">Salvar</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Exibir pop-up com a imagem ao carregar a página
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                imageUrl: 'imagens/reserva_estudios.png',
                imageAlt: 'Reserva de Estúdios',
                showConfirmButton: true,
                confirmButtonText: 'Entendi',
                confirmButtonColor: '#4CAF50',
                customClass: {
                    popup: 'custom-popup',
                },
            });
        });
    </script>
</body>
</html>