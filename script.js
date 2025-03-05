// Autor: Flavio Henrique Guedes Nobre;
// Version: 1.0.1
//"Você é livre para usar e modificar com sabedoria esse código ele é aberto, só não deixe de dar os créditos ao autor"


let currentDate = new Date();
let events = [];

function renderCalendar() {
    const monthYear = document.getElementById('currentMonth');
    const daysContainer = document.getElementById('days');
    daysContainer.innerHTML = '';

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    monthYear.textContent = currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }).replace(/^\w/, c => c.toUpperCase());

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Adicionar dias vazios antes do início do mês
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'day empty';
        daysContainer.appendChild(emptyDay);
    }

    // Renderizar os dias do mês
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'day';
        dayElement.innerHTML = `<span>${day}</span>`;
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        // Adicionar evento de clique para abrir o modal de cadastro em qualquer dia
        dayElement.addEventListener('click', (e) => {
            // Verificar se o clique não foi no "Eventos Cadastrados"
            if (!e.target.classList.contains('event')) {
                openModal(dateStr);
            }
        });
        
        // Verificar se há eventos no dia
        const dayEvents = events.filter(event => event.data === dateStr);
        if (dayEvents.length > 0) {
            const eventsText = document.createElement('div');
            eventsText.className = 'event';
            eventsText.textContent = 'Eventos Cadastrados';
            eventsText.addEventListener('click', () => showEventList(dateStr, dayEvents));
            dayElement.appendChild(eventsText);
        }

        daysContainer.appendChild(dayElement);
    }
}

function showEventList(date, dayEvents) {
    let eventList = '<h2>Eventos do Dia</h2>'; // Título único no topo
    dayEvents.forEach((event, index) => {
        eventList += `<div class="event-detail">${event.titulo} - ${event.horario.slice(0, 5)}</div>`;
        // Adicionar linha apenas entre eventos, não após o último
        /*if (index < dayEvents.length - 1) {
            eventList += '<hr>';
        }*/
    });

    Swal.fire({
        title: '',
        html: eventList,
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#4CAF50', // Cor verde como na imagem
        focusConfirm: false,
        customClass: {
            popup: 'event-list-modal',
            title: 'event-list-title',
            content: 'event-list-content',
            confirmButton: 'swal2-confirm-ok'
        }
    });
}

function openModal(date) {
    const modal = document.getElementById('eventModal');
    const dataInput = document.getElementById('data');
    dataInput.value = date;
    modal.style.display = 'block';

    const close = document.getElementsByClassName('close')[0];
    close.onclick = () => modal.style.display = 'none';

    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = 'none';
    };
}

document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    fetchEvents();
});

document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    fetchEvents();
});

document.getElementById('eventForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    fetch('salvar-evento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            Swal.fire('Erro', result.error, 'error');
        } else {
            Swal.fire('Sucesso', result.message, 'success');
            document.getElementById('eventModal').style.display = 'none';
            e.target.reset(); // Limpa o formulário
            fetchEvents(); // Atualiza os eventos no calendário
        }
    })
    .catch(error => {
        Swal.fire('Erro', 'Erro ao salvar evento: ' + error.message, 'error');
    });
});

function fetchEvents() {
    fetch('listar-eventos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data)) {
                events = data;
                renderCalendar();
            } else {
                console.error('Dados recebidos não são um array:', data);
                Swal.fire('Erro', 'Erro ao carregar eventos: formato inválido', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar eventos:', error);
            Swal.fire('Erro', 'Erro ao carregar eventos: ' + error.message, 'error');
        });
}

// Inicializar o calendário ao carregar a página
window.onload = () => {
    renderCalendar();
    fetchEvents();
};